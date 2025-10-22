const CACHE_NAME = "absensi-app-v1";
const urlsToCache = [
    "/",
    "/login",
    "/dashboard",
    "/css/app.css",
    "/js/app.js",
    "/icons/icon-192x192.png",
    "/icons/icon-512x512.png",
    "/manifest.json",
];

// Install Service Worker
self.addEventListener("install", (event) => {
    console.log("Service Worker: Install");
    event.waitUntil(
        caches
            .open(CACHE_NAME)
            .then((cache) => {
                console.log("Service Worker: Caching Files");
                return cache.addAll(urlsToCache);
            })
            .catch((error) => {
                console.log("Service Worker: Cache failed", error);
            })
    );
});

// Activate Service Worker
self.addEventListener("activate", (event) => {
    console.log("Service Worker: Activate");
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cache) => {
                    if (cache !== CACHE_NAME) {
                        console.log("Service Worker: Clearing Old Cache");
                        return caches.delete(cache);
                    }
                })
            );
        })
    );
});

// Fetch Event
self.addEventListener("fetch", (event) => {
    console.log("Service Worker: Fetching", event.request.url);

    // Skip non-GET requests
    if (event.request.method !== "GET") {
        return;
    }

    // Skip requests with authentication markers
    if (
        event.request.url.includes("/admin/") ||
        event.request.url.includes("/api/") ||
        event.request.url.includes("_token")
    ) {
        return;
    }

    // Use a network-first strategy for navigation/document requests so the app
    // always tries to get fresh HTML (prevents stale UI after actions like check-in).
    const isNavigation =
        event.request.mode === "navigate" ||
        event.request.destination === "document";

    if (isNavigation) {
        event.respondWith(
            fetch(event.request)
                .then((networkResponse) => {
                    // If valid response, update cache for offline fallback
                    if (
                        networkResponse &&
                        networkResponse.status === 200 &&
                        networkResponse.type === "basic"
                    ) {
                        const responseToCache = networkResponse.clone();
                        caches.open(CACHE_NAME).then((cache) => {
                            cache.put(event.request, responseToCache);
                        });
                    }
                    return networkResponse;
                })
                .catch(() => {
                    // Fall back to cache (or offline page)
                    return caches
                        .match(event.request)
                        .then(
                            (cached) => cached || caches.match("/offline.html")
                        );
                })
        );

        return;
    }

    // For other GET requests use cache-first as before (faster assets)
    event.respondWith(
        caches.match(event.request).then((response) => {
            if (response) {
                return response;
            }

            const fetchRequest = event.request.clone();
            return fetch(fetchRequest)
                .then((response) => {
                    if (
                        !response ||
                        response.status !== 200 ||
                        response.type !== "basic"
                    ) {
                        return response;
                    }
                    const responseToCache = response.clone();
                    caches.open(CACHE_NAME).then((cache) => {
                        cache.put(event.request, responseToCache);
                    });
                    return response;
                })
                .catch(() => {
                    if (event.request.destination === "document") {
                        return caches.match("/offline.html");
                    }
                });
        })
    );
});

// Listen for messages from the page (e.g., to clear cache after a write operation)
self.addEventListener("message", (event) => {
    if (!event.data) return;
    if (event.data.type === "CLEAR_CACHE") {
        caches.delete(CACHE_NAME).then((deleted) => {
            console.log("Service Worker: Cache cleared via message", deleted);
        });
    }
});

// Background Sync
self.addEventListener("sync", (event) => {
    if (event.tag === "background-sync") {
        console.log("Service Worker: Background Sync");
        event.waitUntil(doBackgroundSync());
    }
});

function doBackgroundSync() {
    return new Promise((resolve, reject) => {
        // Implement background sync logic here
        // For example, sync offline attendance data
        resolve();
    });
}

// Push Notifications
self.addEventListener("push", (event) => {
    const options = {
        body: event.data
            ? event.data.text()
            : "Notifikasi dari Aplikasi Absensi",
        icon: "/icons/icon-192x192.png",
        badge: "/icons/icon-72x72.png",
        vibrate: [100, 50, 100],
        data: {
            dateOfArrival: Date.now(),
            primaryKey: 1,
        },
        actions: [
            {
                action: "explore",
                title: "Lihat Detail",
                icon: "/icons/checkmark.png",
            },
            {
                action: "close",
                title: "Tutup",
                icon: "/icons/xmark.png",
            },
        ],
    };

    event.waitUntil(
        self.registration.showNotification("Aplikasi Absensi", options)
    );
});

// Notification Click
self.addEventListener("notificationclick", (event) => {
    event.notification.close();

    if (event.action === "explore") {
        // Open the app
        event.waitUntil(clients.openWindow("/"));
    }
});
