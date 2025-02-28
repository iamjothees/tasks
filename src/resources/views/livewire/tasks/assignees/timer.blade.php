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


@script
    <script>
        Alpine.data( 'timer', () => ({
            taskAssignee: null,
            refreshTaskAssignee(){
                !this.isRecentlyRefreshed 
                && this.$wire.refreshTaskAssignee()
                        .then( taskAssignee => this.taskAssignee = taskAssignee)
                        .then(() => {
                            this.isRecentlyRefreshed = true;
                            setTimeout(() => this.isRecentlyRefreshed = false, 2000);
                        })
                        .catch( err => console.log(err) );
            },
            isRecentlyRefreshed: false,
            time: 60,
            interval: null,
            loading: false,
            can: {
                start: false,
                pause: false,
                resume: false,
                stop: false,
                disable(){
                    this.start = this.pause = this.resume = this.stop = false;
                },
            },
            fetchCan(){
                return new Promise((resolve, reject) => {
                    try {
                        Promise.all([
                            this.$wire.canStart().then( (can) => this.can.start = can ),
                            (this.taskAssignee.active_activity ? this.$wire.canPause(this.taskAssignee.active_activity) : this.$wire.canPause())
                                .then( (can) => this.can.pause = can ),
                            (this.taskAssignee.active_activity?.active_pause ? this.$wire.canResume(this.taskAssignee.active_activity?.active_pause) : this.$wire.canResume())
                                .then( (can) => this.can.resume = can ),
                            (this.taskAssignee.active_activity ? this.$wire.canStop(this.taskAssignee.active_activity) : this.$wire.canStop())
                                .then( (can) => this.can.stop = can ),
                        ]).then(() => {
                            resolve(true);
                        });
                    } catch (e) {
                        console.log(e);
                        reject(e);
                    }
                });
            },
            get formattedTime() {
                const hours = Math.floor(this.time / 3600);
                const minutes = (Math.floor(this.time / 60) < 60) ? Math.floor(this.time / 60) : 0;
                const seconds = this.time % 60;
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            start() {
                this.$wire.act(@json(TaskTimerAction::START))
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
            pause() {
                this.$wire.act(@json(TaskTimerAction::PAUSE), this.taskAssignee.active_activity.id)
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
            resume() {
                this.$wire.act(@json(TaskTimerAction::RESUME), this.taskAssignee.active_activity.active_pause.id)
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
            stop() {
                if (confirm('Are you sure you want to stop the timer?'))
                this.$wire.act(@json(TaskTimerAction::STOP), this.taskAssignee.active_activity.id)
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
            reset() {
                if (confirm('Are you sure you want to reset the timer?'))
                this.$wire.act(@json(TaskTimerAction::RESET), this.taskAssignee.active_activity.id)
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
            resetComponent(){
                this.time = 0;
                this.can.disable();
                clearInterval(this.interval);
                this.interval = null;
            },
            init: function() {
                const that = this;
                const updateTimer = function(){
                    that.resetComponent();
                    that.loading = true;

                    if (!that.taskAssignee) return;
                    that.time = that.taskAssignee.active_activity?.time_taken_in_seconds ?? 0;
                    if (that.taskAssignee.latest_activity?.is_running){
                        that.interval = setInterval(() =>  that.time++ , 1000);
                    }
                    that.fetchCan()
                        .then(() => that.loading = false)
                        .catch(e => {
                            that.resetComponent();
                            console.error(e);
                        })
                }
                this.$watch( 'taskAssignee', updateTimer );
                this.taskAssignee = JSON.parse(this.$root.getAttribute('data-task_assignee'));

                addEventListener("focus", () => this.refreshTaskAssignee() );
            },
            root: {
                ['@mouseenter'](){ this.refreshTaskAssignee(); } 
            }
        }));
    </script>
@endscript