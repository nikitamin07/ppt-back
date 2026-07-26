<x-filament::page>
    <x-filament::section>
        <x-slot name="heading">Экспорт цен</x-slot>
        <x-slot name="description">
            Выгрузка всех товаров с текущими ценами в Excel. Отредактируйте цены в файле (id и названия не трогайте) и загрузите его обратно ниже.
        </x-slot>

        <x-filament::button
            wire:click="export"
            wire:loading.attr="disabled"
            wire:target="export"
            icon="heroicon-o-arrow-down-tray"
        >
            Экспорт товаров с ценами в Excel
        </x-filament::button>
    </x-filament::section>

    <x-filament::section>
        <x-slot name="heading">Импорт цен</x-slot>
        <x-slot name="description">
            Заполните для товара либо объёмные цены (2–3 столбца), либо обычную цену, либо обычную вместе со скидкой.
            Товары с противоречивыми ценами будут пропущены, о них сообщим после импорта.
        </x-slot>

        {{ $this->form }}

        <div class="mt-4">
            <x-filament::button
                wire:click="import"
                wire:loading.attr="disabled"
                wire:target="import"
                icon="heroicon-o-arrow-up-tray"
            >
                Импорт цен Excel
            </x-filament::button>
        </div>
    </x-filament::section>
</x-filament::page>
