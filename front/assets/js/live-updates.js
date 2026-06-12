(function () {
    const listeners = new Map();
    let socket = null;
    let retryTimer = null;

    function wsUrl() {
        const protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        return `${protocol}//${window.location.hostname}:8090/ws`;
    }

    function connect() {
        if (socket && (socket.readyState === WebSocket.OPEN || socket.readyState === WebSocket.CONNECTING)) {
            return;
        }

        socket = new WebSocket(wsUrl());
        socket.addEventListener('message', event => {
            let message;
            try {
                message = JSON.parse(event.data);
            } catch (error) {
                return;
            }

            const topic = message.topic || '*';
            (listeners.get(topic) || []).forEach(fn => fn(message));
            (listeners.get('*') || []).forEach(fn => fn(message));
        });

        socket.addEventListener('close', scheduleReconnect);
        socket.addEventListener('error', scheduleReconnect);
    }

    function scheduleReconnect() {
        clearTimeout(retryTimer);
        retryTimer = setTimeout(connect, 3000);
    }

    window.LiveUpdates = {
        on(topic, callback) {
            if (!listeners.has(topic)) {
                listeners.set(topic, []);
            }
            listeners.get(topic).push(callback);
            connect();
        }
    };
})();
