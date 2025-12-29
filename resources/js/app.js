import './bootstrap';
import './echo';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Store for online users (presence channel)
Alpine.store('onlineUsers', {
    users: [],
    isOnline(userId) {
        return this.users.some(u => u.id === userId);
    }
});

Alpine.start();

// Join presence channel for tracking online users
if (window.Echo) {
    window.Echo.join('users')
        .here((users) => {
            Alpine.store('onlineUsers').users = users;
        })
        .joining((user) => {
            const store = Alpine.store('onlineUsers');
            if (!store.users.some(u => u.id === user.id)) {
                store.users.push(user);
            }
        })
        .leaving((user) => {
            const store = Alpine.store('onlineUsers');
            store.users = store.users.filter(u => u.id !== user.id);
        });
}
