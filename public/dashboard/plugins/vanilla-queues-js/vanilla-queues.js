var VanillaQueues = /** @class */ (function () {
    function VanillaQueues(queueCountInit) {
        if (queueCountInit && parseInt(queueCountInit.toString()) >= 1)
            this._queueCount = queueCountInit;
        else
            this._queueCount = 5;
        this._stackJobs = [];
    }
    VanillaQueues.prototype.addJob = function (callback, transferData) {
        this._stackJobs.push({ callback: callback, data: transferData });
    };
    /**
     * name
     */
    VanillaQueues.prototype.runJobs = function () {
        this._stackJobs = this._stackJobs.reverse();
        this.queueActual = this._queueCount;
        for (var index = 0; index < this._queueCount; index++) {
            var runInstance = this._stackJobs.pop();
            runInstance.callback(runInstance.data, index);
        }
    };
    VanillaQueues.prototype.jobDone = function () {
        if (this._stackJobs.length !== 0) {
            var runInstance = this._stackJobs.pop();
            runInstance.callback(runInstance.data, this._queueCount++);
        }
        else if (--this.queueActual === 0)
            this.doneCallback();
    };
    VanillaQueues.prototype.isDone = function (callback) {
        this.doneCallback = callback;
    };
    Object.defineProperty(VanillaQueues.prototype, "queueCount", {
        get: function () {
            return this._queueCount;
        },
        set: function (value) {
            this._queueCount = value;
        },
        enumerable: true,
        configurable: true
    });
    return VanillaQueues;
}());
// export { VanillaQueues, queueCallback, IVanillaQueues };
