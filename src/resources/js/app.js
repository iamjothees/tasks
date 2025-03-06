import './bootstrap';
import '@fortawesome/fontawesome-free/js/all.js';


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
                    (this.taskAssignee.active_pause ? this.$wire.canResume(this.taskAssignee.active_pause) : this.$wire.canResume())
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
        this.$wire.act('start')
            .then( taskAssignee => this.taskAssignee = taskAssignee)
            .catch( err => console.log(err) );
    },
    pause() {
        this.$wire.act('pause', this.taskAssignee.active_activity.id)
            .then( taskAssignee => this.taskAssignee = taskAssignee)
            .catch( err => console.log(err) );
    },
    resume() {
        this.$wire.act('resume', this.taskAssignee.active_activity.active_pause.id)
            .then( taskAssignee => this.taskAssignee = taskAssignee)
            .catch( err => console.log(err) );
    },
    stop() {
        if (confirm('Are you sure you want to complete the activity?'))
        this.$wire.act('stop', this.taskAssignee.active_activity.id)
            .then( taskAssignee => this.taskAssignee = taskAssignee)
            .catch( err => console.log(err) );
    },
    reset() {
        if (confirm('Are you sure you want to reset the timer?'))
        this.$wire.act('reset', this.taskAssignee.active_activity.id)
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

