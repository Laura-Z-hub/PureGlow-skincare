(function () {
    const endpoint = "../api/admin.php";
    const pollMs = 2000;
    const defaultKeys = ["orders", "contacts", "bookings", "subscriptions"];
    const body = document.body;

    if (!body) {
        return;
    }

    let baseline = parseCounts(body.dataset.liveCounts);
    const pageKey = body.dataset.liveKey;
    let originalTitle = document.title;

    function parseCounts(value) {
        if (!value) {
            return null;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            return null;
        }
    }

    function toNumber(value) {
        const number = Number(value);
        return Number.isFinite(number) ? number : 0;
    }

    function getWatchedKeys() {
        return pageKey ? [pageKey] : defaultKeys;
    }

    function labelFor(key) {
        return key.charAt(0).toUpperCase() + key.slice(1);
    }

    function formatValue(key, value) {
        if (key === "money") {
            return "EUR " + Number(value || 0).toFixed(2);
        }

        return String(toNumber(value));
    }

    function updateCard(key, value) {
        const valueNode = document.querySelector(`[data-live-value="${key}"]`);
        if (valueNode) {
            valueNode.textContent = formatValue(key, value);
        }
    }

    function updateBadge(key, diff) {
        const card = document.querySelector(`[data-live-card="${key}"]`);
        if (!card || diff <= 0) {
            return;
        }

        let badge = card.querySelector(`[data-live-badge="${key}"]`);
        if (!badge) {
            badge = document.createElement("span");
            badge.className = "notify-badge";
            badge.dataset.liveBadge = key;
            card.prepend(badge);
        }

        badge.textContent = diff;
        card.classList.add("live-pulse");
        window.setTimeout(() => card.classList.remove("live-pulse"), 1800);
    }

    function showToast(key, diff) {
        let toast = document.getElementById("adminLiveToast");
        if (!toast) {
            toast = document.createElement("div");
            toast.id = "adminLiveToast";
            toast.className = "admin-live-toast";
            document.body.appendChild(toast);
        }

        const text = diff === 1 ? `1 new ${key.slice(0, -1)}` : `${diff} new ${key}`;
        toast.innerHTML = `
            <strong>${labelFor(key)} update</strong>
            <span>${text} just arrived.</span>
            <a href="${key}.php">Open ${labelFor(key)}</a>
        `;
        toast.classList.add("show");
        document.title = `(${diff}) ${labelFor(key)} - ${originalTitle}`;
        window.setTimeout(() => toast.classList.remove("show"), 7000);
    }

    async function pollAdminCounts() {
        try {
            const response = await fetch(endpoint, {
                cache: "no-store",
                credentials: "same-origin",
            });

            if (!response.ok) {
                return;
            }

            const nextCounts = await response.json();

            if (!baseline) {
                baseline = nextCounts;
                return;
            }

            Object.keys(nextCounts).forEach((key) => updateCard(key, nextCounts[key]));

            getWatchedKeys().forEach((key) => {
                const diff = toNumber(nextCounts[key]) - toNumber(baseline[key]);

                if (diff > 0) {
                    updateBadge(key, diff);
                    showToast(key, diff);
                }
            });

            baseline = nextCounts;
        } catch (error) {
            // Keep the admin page usable if a temporary request fails.
        }
    }

    window.setTimeout(pollAdminCounts, 1000);
    window.setInterval(pollAdminCounts, pollMs);
})();
