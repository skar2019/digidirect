define(['jquery'], function($) {
    return {
        init: function() {
            const safeText = t => t == null ? '' : String(t);

            function createCard(event) { /* same as before */ }
            function createCarouselItem(event) { /* same as before */ }
            function initCarouselControls() { /* same as before */ }

            async function fetchEvents() {
                const list = $('#event-list');
                const carousel = $('#past-carousel');

                try {
                    // Fetch live events
                    const liveData = await $.getJSON('/eventbrite/events/fetch?type=live');
                    list.empty();
                    if (liveData.success && liveData.events.length) {
                        liveData.events.slice(0,3).forEach(ev => list.append(createCard(ev)));
                    } else {
                        list.html('<p class="muted">No live events found.</p>');
                    }

                    // Fetch past events
                    const pastData = await $.getJSON('/eventbrite/events/fetch?type=past');
                    carousel.empty();
                    if (pastData.success && pastData.events.length) {
                        pastData.events.forEach(ev => carousel.append(createCarouselItem(ev)));
                    } else {
                        carousel.html('<p class="muted">No past events found.</p>');
                    }

                    initCarouselControls();
                } catch(e) {
                    console.error("Error loading events", e);
                    list.html('<p class="muted">Error loading events.</p>');
                    carousel.html('<p class="muted">Error loading past events.</p>');
                }
            }

            fetchEvents();
        }
    };
});
