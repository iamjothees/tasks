<div x-data="timer" data-task_assignee="{{ json_encode($taskAssignee->toArray()) }}" x-bind="root" class="mt-3 w-full" >
    <div class="p-4 bg-sky-700 dark:bg-sky-950 text-white border-2 border-slate-500 m-3 rounded-xl shadow-md space-y-4" :class="loading ?`animate-pulse` : ``">
        <div class="text-center text-3xl font-bold">
            <span x-text="formattedTime"></span>
        </div>
        <div class="flex justify-center">
            <div class="flex-grow flex flex-row gap-3 sm:gap-0 flex-wrap justify-around sm:justify-between items-center max-w-[50%]">
                <x-tasks.assignees.timers.action-button @click.stop="reset" class="order-2 sm:order-1 w-10 h-10" x-bind:disabled="!can.stop" 
                    x-bind:class="can.stop ? `border-2 bg-gray-500 dark:bg-gray-500/50 text-gray-300/90 dark:text-gray-300/60 border-gray-300/90 dark:border-gray-300/60 hover:bg-gray-500/75 hover:dark:bg-gray-500/40  hover:text-gray-300  hover:dark:text-gray-300/60` : `bg-gray-600/75 text-gray-300/50`"
                >
                    <i class="fa-solid fa-rotate-right fa-lg"></i>
                </x-tasks.assignees.timers.action-button>

                {{-- Start --}}
                <x-tasks.assignees.timers.action-button @click.stop="start" x-show="can.start" class="order-1 sm:order-2 min-w-[100%] sm:min-w-0  w-14 h-14 border-2 ps-1" x-bind:disabled="!can.start" x-bind:class="can.start ? `bg-sky-950/50 text-sky-300 border-sky-300 hover:bg-sky-700/15 hover:text-sky-400 hover:border-sky-400` : `bg-sky-800/15 text-sky-900 border-sky-900`">
                    <i class="fa-solid fa-play fa-2xl"></i>
                </x-tasks.assignees.timers.action-button>
                {{-- Pause --}}
                <x-tasks.assignees.timers.action-button @click.stop="pause" x-show="can.pause" class="order-1 sm:order-2 min-w-[100%] sm:min-w-0 w-14 h-14 border-2" x-bind:disabled="!can.pause" x-bind:class="can.pause ? `bg-sky-950/50 text-sky-300 border-sky-300 hover:bg-sky-700/15 hover:text-sky-400 hover:border-sky-400` : `bg-sky-800/15 text-sky-900 border-sky-900`">
                    <i class="fa-solid fa-pause fa-2xl"></i>
                </x-tasks.assignees.timers.action-button>
                {{-- Resume --}}
                <x-tasks.assignees.timers.action-button @click.stop="resume" x-show="can.resume" class="order-1 sm:order-2 min-w-[100%] sm:min-w-0 w-14 h-14 border-2 ps-1" x-bind:disabled="!can.resume" x-bind:class="can.resume ? `bg-sky-950/50 text-sky-300 border-sky-300 hover:bg-sky-700/15 hover:text-sky-400 hover:border-sky-400` : `bg-sky-800/15 text-sky-900 border-sky-900`">
                    <i class="fa-solid fa-play fa-2xl"></i>
                </x-tasks.assignees.timers.action-button>
                {{-- Placeholder --}}
                <x-tasks.assignees.timers.action-button x-show="!(can.start || can.pause || can.resume)" class="order-1 sm:order-2 min-w-[100%] sm:min-w-0 w-14 h-14 bg-sky-800/15 text-sky-900 border-sky-900" disabled >
                    <i class="fa-solid fa-play fa-2xl"></i>
                </x-tasks.assignees.timers.action-button>
    
                
                <x-tasks.assignees.timers.action-button @click.stop="stop" class="order-3 w-10 h-10" x-bind:disabled="!can.stop" x-bind:class="can.stop ? `border-2 bg-red-800/50 text-red-600 border-red-600 hover:bg-red-800/75 hover:text-red-500` : `bg-red-800/35 text-red-100/45`">
                    <i class="fa-solid fa-stop fa-lg"></i>
                </x-tasks.assignees.timers.action-button>
                
                {{-- <button @click.stop="pause" :disabled="!can.pause" class="px-4 py-2 border rounded" :class="can.pause ? ` text-amber-700 border-amber-700 hover:bg-amber-700 hover:text-white` : `border-gray-500`">Pause</button>
                <button @click.stop="resume" :disabled="!can.resume" class="px-4 py-2 border rounded" :class="can.resume ? ` text-blue-700 border-blue-700 hover:bg-blue-700 hover:text-white` : `border-gray-500`">Resume</button>
                <button @click.stop="stop" :disabled="!can.stop" class="px-4 py-2 border rounded" :class="can.stop ? ` text-red-700 border-red-700 hover:bg-red-700 hover:text-white` : `border-gray-500`">Stop</button> --}}
            </div>
        </div>
    </div>
</div>