<div x-data="timer" data-task_assignee="{{ json_encode($taskAssignee->toArray()) }}" x-bind="root" 
    class="p-4 w-96 h-48 bg-black border border-white m-3 rounded-xl shadow-md space-y-4" :class="loading ?`animate-pulse` : ``"
>
    <div class="flex flex-col min-w-full min-h-full justify-between">
        <div class="text-center text-2xl font-bold">
            <span x-text="formattedTime"></span>
        </div>
        <div class="flex justify-center space-x-2">
            <button @click.stop="start" :disabled="!can.start" class="px-4 py-2 border rounded" :class="can.start ? ` text-green-700 border-green-700 hover:bg-green-700 hover:text-white` : `border-gray-500`">Start</button>
            <button @click.stop="pause" :disabled="!can.pause" class="px-4 py-2 border rounded" :class="can.pause ? ` text-amber-700 border-amber-700 hover:bg-amber-700 hover:text-white` : `border-gray-500`">Pause</button>
            <button @click.stop="resume" :disabled="!can.resume" class="px-4 py-2 border rounded" :class="can.resume ? ` text-blue-700 border-blue-700 hover:bg-blue-700 hover:text-white` : `border-gray-500`">Resume</button>
            <button @click.stop="stop" :disabled="!can.stop" class="px-4 py-2 border rounded" :class="can.stop ? ` text-red-700 border-red-700 hover:bg-red-700 hover:text-white` : `border-gray-500`">Stop</button>
        </div>
        <button @click.stop="reset" :disabled="!can.stop" class="min-w-full px-4 py-2 border text-white rounded" :class="can.stop ? `bg-red-700 hover:bg-red-900` : `bg-gray-500`">Reset</button>
    </div>
</div>

@script
    <script>
        Alpine.data( 'timer', () => ({
            taskAssignee: null,
            refreshTaskAssignee(){
                this.$wire.refreshTaskAssignee()
                    .then( taskAssignee => this.taskAssignee = taskAssignee)
                    .catch( err => console.log(err) );
            },
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
                    that.time = that.taskAssignee.latest_activity?.time_taken_in_seconds ?? 0;
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