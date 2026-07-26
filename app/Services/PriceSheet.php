<?php

namespace App\Services;

use App\Models\Product;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Writer\XLSX\Writer;
use RuntimeException;
use Throwable;

/**
 * Экспорт/импорт цен товаров в XLSX. 7 столбцов: id, название и пять цен (рубли).
 * Импорт меняет только цены — id и название служат ориентиром и не трогаются.
 */
class PriceSheet
{
    private const HEADER = [
        'ID', 'Название', 'Обычная цена, руб', 'Цена со скидкой, руб',
        'Объём: малый', 'Объём: средний', 'Объём: большой',
    ];

    /** Записать все товары с текущими ценами в файл. */
    public function write(string $path): void
    {
        $writer = new Writer();
        $writer->openToFile($path);
        $writer->addRow(Row::fromValues(self::HEADER));

        foreach (Product::orderBy('id')->get() as $p) {
            $row = $p->is_volume_price
                ? [$p->id, $p->name, '', '', self::rub($p->volume_price_low), self::rub($p->volume_price_medium), self::rub($p->volume_price_high)]
                : [$p->id, $p->name, self::rub($p->price), self::rub($p->discount_price), '', '', ''];
            $writer->addRow(Row::fromValues($row));
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

    /** Пять ценовых ячеек в копейки; null — пусто, исключение — мусор. */
    private static function parseRow(array $cells): array
    {
        return [
            'regular' => self::money($cells[2] ?? ''),
            'discount' => self::money($cells[3] ?? ''),
            'low' => self::money($cells[4] ?? ''),
            'medium' => self::money($cells[5] ?? ''),
            'high' => self::money($cells[6] ?? ''),
        ];
    }

    /** Причина отбраковки строки или null, если цены корректны. */
    private static function validate(array $v): ?string
    {
        $hasVolume = $v['low'] !== null || $v['medium'] !== null || $v['high'] !== null;

        if ($hasVolume && ($v['regular'] !== null || $v['discount'] !== null)) {
            return 'указаны и объёмные цены, и обычная — неясно, какую ставить';
        }

        if ($hasVolume) {
            return ($v['low'] === null || $v['medium'] === null)
                ? 'для объёмных цен нужны малый и средний объём'
                : null;
        }

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
        if ($v['low'] !== null || $v['medium'] !== null || $v['high'] !== null) {
            $product->is_volume_price = true;
            $product->volume_price_low = $v['low'];
            $product->volume_price_medium = $v['medium'];
            $product->volume_price_high = $v['high'];
        } else {
            $product->is_volume_price = false;
            $product->price = $v['regular'];
            $product->discount_price = $v['discount'];
        }
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
