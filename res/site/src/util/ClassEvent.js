class ClassEvent
{
    constructor()
    {
        this._events = {};
    }

    on(eventName, fn) // Fica esperando o trigger disparar
    {
        if (!this._events[eventName]) this._events[eventName] = new Array();

        this._events[eventName].push(fn);
    }

    trigger() // Dispara o evento
    {
        let args = [...arguments]; // quantidade ilimitada de args
        let eventName = args.shift();

        args.push(new Event(eventName));

        if (this._events[eventName] instanceof Array)
        {
            this._events[eventName].forEach(fn => {
                fn.apply(null, args);
            });
        }
    }

}