<div class="max-w-7xl mx-auto py-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8 px-2">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">My Tasks</h1>
            <p class="text-sm text-slate-500 mt-1">Manage and track your assigned work</p>
        </div>
        <div class="flex items-center gap-3">
            <flux:select wire:model.live="filterStatus" class="w-40 bg-white border-none shadow-sm rounded-lg" size="sm">
                <flux:select.option value="all">Active Tasks</flux:select.option>
                <flux:select.option value="pending">Pending</flux:select.option>
                <flux:select.option value="in_progress">In Progress</flux:select.option>
                <flux:select.option value="completed">Completed</flux:select.option>
            </flux:select>
            <flux:modal.trigger name="new-task">
                <flux:button variant="primary" icon="plus" class="rounded-xl shadow-sm">New Task</flux:button>
            </flux:modal.trigger>
        </div>
    </div>

    <!-- Bento Box Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @forelse($tasks as $task)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col hover:shadow-md transition-all duration-200">
                <!-- Top row: Status & Priority -->
                <div class="flex items-center justify-between mb-3">
                    @if($task->status === 'completed')
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold bg-emerald-50 text-emerald-700">
                            <flux:icon.check-circle class="w-3.5 h-3.5" variant="solid" />
                            Completed
                        </span>
                    @elseif($task->status === 'in_progress')
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold bg-blue-50 text-blue-700">
                            <flux:icon.arrow-path class="w-3.5 h-3.5" variant="solid" />
                            In Progress
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-semibold bg-slate-100 text-slate-700">
                            <flux:icon.clock class="w-3.5 h-3.5" variant="solid" />
                            Pending
                        </span>
                    @endif

                    @if($task->priority === 'urgent')
                        <span class="text-[10px] font-bold text-red-600 uppercase tracking-wider bg-red-50 px-2 py-0.5 rounded-full">Urgent</span>
                    @elseif($task->priority === 'high')
                        <span class="text-[10px] font-bold text-orange-600 uppercase tracking-wider bg-orange-50 px-2 py-0.5 rounded-full">High</span>
                    @endif
                </div>

                <!-- Title & Desc -->
                <h3 class="text-sm font-bold text-slate-800 mb-2 line-clamp-2">{{ $task->title }}</h3>
                <p class="text-xs text-slate-500 mb-4 line-clamp-3 flex-1">{{ $task->description }}</p>

                <!-- Footer: Date & Actions -->
                <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                    <div class="flex items-center gap-2 text-[11px] font-medium text-slate-400">
                        <flux:icon.calendar class="w-3.5 h-3.5" />
                        {{ $task->due_date ? $task->due_date->format('M d, Y') : 'No date' }}
                    </div>
                    
                    @if($task->status !== 'completed')
                        <button wire:click="markAsComplete({{ $task->id }})" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-700 flex items-center gap-1 transition-colors">
                            <flux:icon.check class="w-3.5 h-3.5" />
                            Done
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl shadow-sm border border-slate-100 p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <flux:icon.check-circle class="w-8 h-8 text-slate-300" variant="outline" />
                </div>
                <h3 class="text-base font-bold text-slate-800 mb-1">No tasks found</h3>
                <p class="text-sm text-slate-500">You don't have any tasks matching the current filter.</p>
            </div>
        @endforelse
    </div>

    <!-- New Task Modal -->
    <flux:modal name="new-task" class="min-w-[400px] md:w-96">
        <form wire:submit="createTask" class="space-y-6">
            <div>
                <flux:heading size="lg">Create New Task</flux:heading>
                <flux:subheading>Add a new task to your workload.</flux:subheading>
            </div>

            <flux:input wire:model="newTaskTitle" label="Title" placeholder="What needs to be done?" required />

            <flux:textarea wire:model="newTaskDescription" label="Description" placeholder="Optional details..." />

            <div class="grid grid-cols-2 gap-4">
                <flux:select wire:model="newTaskPriority" label="Priority">
                    <flux:select.option value="normal">Normal</flux:select.option>
                    <flux:select.option value="high">High</flux:select.option>
                    <flux:select.option value="urgent">Urgent</flux:select.option>
                </flux:select>

                <flux:input type="date" wire:model="newTaskDueDate" label="Due Date" />
            </div>

            <div class="flex gap-2 justify-end mt-4">
                <flux:modal.close>
                    <flux:button variant="ghost">Cancel</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary">Create Task</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
