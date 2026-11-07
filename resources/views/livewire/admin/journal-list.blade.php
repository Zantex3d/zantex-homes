<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold">Journal Entries</h1>
        <a href="#" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
            New Entry
        </a>
    </div>

    <div class="bg-white dark:bg-slate-800 p-6 rounded-lg shadow border border-slate-200 dark:border-slate-700">
        <div class="flex gap-4 mb-6">
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Search description..."
                class="flex-1 px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-slate-700 dark:text-white"
            >
            <select wire:model.live="status" class="px-4 py-2 border border-slate-300 dark:border-slate-600 rounded-lg dark:bg-slate-700 dark:text-white">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="posted">Posted</option>
            </select>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b border-slate-200 dark:border-slate-700">
                    <tr class="text-left text-sm font-semibold">
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">Posted By</th>
                        <th class="px-4 py-3">Lines</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                    @forelse ($journals as $entry)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <td class="px-4 py-3 text-sm">{{ $entry->entry_date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 font-medium">{{ Str::limit($entry->description, 50) }}</td>
                            <td class="px-4 py-3 text-sm">{{ $entry->postedBy->name ?? 'System' }}</td>
                            <td class="px-4 py-3 text-sm font-mono">{{ $entry->lines()->count() }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                    {{ $entry->status === 'posted' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' }}">
                                    {{ ucfirst($entry->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View</a>
                                    @if ($entry->status === 'draft')
                                        <a href="#" class="text-green-600 dark:text-green-400 hover:underline text-sm">Post</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                                No journal entries found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $journals->links() }}
        </div>
    </div>
</div>
