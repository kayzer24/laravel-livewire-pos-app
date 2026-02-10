<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @livewire(\App\Livewire\ApplicationStats::class)
        <div>
            @livewire(\App\Livewire\SalesChart::class)
        </div>
        <div>
            @livewire(\App\Livewire\LatestSales::class)
        </div>
        <div>
            @livewire(\App\Livewire\LatestCustomers::class)
        </div>
    </div>
</x-layouts::app>
