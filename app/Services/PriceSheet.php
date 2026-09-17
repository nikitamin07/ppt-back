<?php

namespace App\Services;

use App\Models\Product;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;
use Throwable;

/**
 * Экспорт/импорт цен товаров в XLSX. 5 столбцов: id, название, единица измерения и две цены (рубли).
 * Импорт меняет только цены — id, название и единица измерения служат ориентиром и не трогаются.
 * Товары с объёмными ценами в файл не попадают — их цены здесь не редактируются.
 */
class PriceSheet
{
    private const HEADER = [
        'ID', 'Название', 'Единицы измерения', 'Обычная цена, руб', 'Цена со скидкой, руб',
    ];

    private const COLUMN_WIDTHS = [1 => 8, 2 => 55, 3 => 20, 4 => 22, 5 => 22];

    /** Записать активные товары без объёмных цен в файл. */
    public function write(string $path): void
    {
        $writer = new Writer();
        foreach (self::COLUMN_WIDTHS as $column => $width) {
            $writer->getOptions()->setColumnWidth((float) $width, $column);
        }
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(self::HEADER));

        foreach (Product::orderBy('id')->get() as $p) {
            if ($p->is_volume_price) {
                continue;
            }

            $writer->addRow(Row::fromValues([$p->id, $p->name, $p->price_unit, self::rub($p->price), self::rub($p->discount_price)]));
        }

        $writer->close();
    }

    /**
     * Применить цены из файла. Возвращает ['applied' => int, 'skipped' => string[]].
     * Строка с ошибкой пропускается, причина попадает в skipped.
     */
    public function import(string $path): array
    {
        $applied = 0;
        $skipped = [];

        $reader = new Reader();
        $reader->open($path);

        foreach ($reader->getSheetIterator() as $sheet) {
            $rowNum = 0;
            foreach ($sheet->getRowIterator() as $row) {
                $rowNum++;
                if ($rowNum === 1) {
                    continue; // шапка
                }

                $cells = $row->toArray();
                $id = trim((string) ($cells[0] ?? ''));
                if ($id === '') {
                    continue; // пустая строка в хвосте
                }

                $label = "строка {$rowNum} (ID {$id})";
                $product = Product::find((int) $id);
                if (! $product) {
                    $skipped[] = "{$label}: товар не найден";
                    continue;
                }

                try {
                    $vals = self::parseRow($cells);
                } catch (RuntimeException $e) {
                    $skipped[] = "{$label}: ".$e->getMessage();
                    continue;
                }

                if ($error = self::validate($vals)) {
                    $skipped[] = "{$label}: {$error}";
                    continue;
                }

                self::apply($product, $vals);

                try {
                    $product->save();
                    $applied++;
                } catch (Throwable $e) {
                    $skipped[] = "{$label}: не удалось сохранить ({$e->getMessage()})";
                }
            }

            break; // только первый лист
        }

        $reader->close();

        return ['applied' => $applied, 'skipped' => $skipped];
    }

    /** Две ценовые ячейки в копейки; null — пусто, исключение — мусор. */
    private static function parseRow(array $cells): array
    {
        return [
            'regular' => self::money($cells[3] ?? ''),
            'discount' => self::money($cells[4] ?? ''),
        ];
    }

    /** Причина отбраковки строки или null, если цены корректны. */
    private static function validate(array $v): ?string
    {
        if ($v['regular'] !== null) {
            return ($v['discount'] !== null && $v['discount'] >= $v['regular'])
                ? 'цена со скидкой не меньше обычной'
                : null;
        }

        return $v['discount'] !== null
            ? 'указана только цена со скидкой, без обычной'
            : 'не заполнена ни одна цена';
    }

    /** Разложить цены по полям; остальное досчитают хуки модели Product. */
    private static function apply(Product $product, array $v): void
    {
        $product->is_volume_price = false;
        $product->price = $v['regular'];
        $product->discount_price = $v['discount'];
    }

    /** Рубли (число или '') из копеек. */
    private static function rub(?int $kopecks): float|string
    {
        return $kopecks === null ? '' : $kopecks / 100;
    }

    /** Рубли из ячейки → копейки; null для пустой, исключение для нечисловой. */
    private static function money(mixed $cell): ?int
    {
        $raw = trim((string) $cell);
        $s = str_replace(',', '.', preg_replace('/\s+/u', '', $raw));

        if ($s === '') {
            return null;
        }
        if (! is_numeric($s)) {
            throw new RuntimeException("нечисловое значение цены «{$raw}»");
        }
        if ((float) $s < 0) {
            throw new RuntimeException('цена не может быть отрицательной');
        }

        return (int) round((float) $s * 100);
    }
}
