class MasterSlaveCheckbox {
    constructor(options = {master: null, slaves: [], masterStyling: true}) {
        this.master = options.master;
        this.slaves = options.slaves;
        this.masterStyling = options?.masterStyling ?? true;
        this.master.addEventListener('change', this.onMasterChange.bind(this));
        this.slaves.forEach(slave => {
            slave.addEventListener('change', this.onSlaveChange.bind(this));
        });
        if (this.slaves.length === 0) {
            this.master.disabled = true;
            return;
        }

        this.master.checked = this.slaves.every(slave => slave.checked);
        this.master.dispatchEvent(new Event("manual_change"));
        this.updateMasterBackgroundStyle();
    }
    onMasterChange(e) {
        this.slaves.forEach(slave => {
            slave.checked = this.master.checked;
        });
        this.updateMasterBackgroundStyle();
    }
    onSlaveChange(e) {
        this.master.checked = this.slaves.every(slave => slave.checked);
        this.master.dispatchEvent(new Event("manual_change"));
        this.updateMasterBackgroundStyle();
    }
    updateMasterBackgroundStyle() {
        if (!this.masterStyling) {
            this.master.classList.remove("half-checked");
            return;
        }
        const totalSlavesCount = this.slaves.length;
        if (totalSlavesCount === 0) {
            return;
        }

        const checkSlavesCount = this.slaves.filter(slave => slave.checked).length;

        if (checkSlavesCount > 0 && checkSlavesCount < totalSlavesCount) {
            this.master.classList.add("half-checked");
        } else {
            this.master.classList.remove("half-checked");
        }
    }
}
