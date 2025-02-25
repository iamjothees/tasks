<div x-data="timerComponent()" class="p-4 max-w-sm mx-auto bg-black border border-white m-3 rounded-xl shadow-md space-y-4">
    <div class="text-center text-2xl font-bold">
        <span x-text="formattedTime"></span>
    </div>
    <div class="flex justify-center space-x-2">
        <button @click="start" class="px-4 py-2 border text-white rounded" :class="can.start ? `border-green-700 hover:bg-green-700` : `border-gray-500`">Start</button>
        <button @click="start" class="px-4 py-2 border border-amber-500 text-white rounded" :class="can.resume ? `border-amber-700 hover:bg-amber-700` : `border-gray-500`">Pause</button>
        <button @click="start" class="px-4 py-2 border border-blue-500 text-white rounded" :class="can.pause ? `border-blue-700 hover:bg-blue-700` : `border-gray-500`">Resume</button>
        <button @click="stop" class="px-4 py-2 border border-red-500 text-white rounded" :class="can.stop ? `border-red-700 hover:bg-red-700` : `border-gray-500`">Stop</button>
    </div>
</div>

<script>
    function timerComponent() {
        return {
            time: 0,
            interval: null,
            can: {
                start: true,
                pause: false,
                resume: false,
                stop: false,
            },
            get formattedTime() {
                const hours = Math.floor(this.time / 3600);
                const minutes = (Math.floor(this.time / 60) < 60) ? Math.floor(this.time / 60) : 0;
                const seconds = this.time % 60;
                return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            },
            start() {
                if (this.interval) return;
                this.interval = setInterval(() => {
                    this.time++;
                }, 1000);
            },
            stop() {
                clearInterval(this.interval);
                this.interval = null;
            },
            reset() {
                this.stop();
                this.time = 0;
            }
        }
    }
</script>