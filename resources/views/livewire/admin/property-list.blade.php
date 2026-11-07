<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Properties</h1>
        <a href="#" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            Add Property
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
        <div class="flex gap-4 mb-6">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search by code or name..."
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
            >
            <select wire:model.live="sortBy" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="name">Sort by Name</option>
                <option value="property_code">Sort by Code</option>
                <option value="created_at">Sort by Date</option>
            </select>
            <button wire:click="setSortBy('{{ $sortBy }}')" class="px-4 py-2 bg-slate-200 dark:bg-slate-700 rounded-lg hover:bg-slate-300 dark:hover:bg-slate-600 transition">
                {{ $sortDir === 'asc' ? '↑' : '↓' }}
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-left text-sm font-semibold">
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Estate</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Units</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($properties as $property)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3 font-mono text-sm">{{ $property->property_code }}</td>
                            <td class="px-4 py-3 font-medium">{{ $property->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $property->estate->name }}</td>
                            <td class="px-4 py-3 text-sm">{{ $property->propertyType->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $property->units_count ?? $property->units()->count() }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $property->status === 'active' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-slate-100 dark:bg-slate-700 text-slate-700 dark:text-slate-300' }}">
                                    {{ ucfirst($property->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View</a>
                                    <a href="#" class="text-slate-600 dark:text-slate-400 hover:underline text-sm">Edit</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                No properties found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $properties->links() }}
        </div>
    </div>
</div>
