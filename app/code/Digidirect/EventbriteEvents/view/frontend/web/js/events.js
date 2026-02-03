function safeText(t) {
      return (t == null) ? '' : String(t);
    }

    function createCard(event) {
      const card = document.createElement('article');
      card.className = 'event-card';
      
      const media = document.createElement('div');
      media.className = 'media';
      const img = document.createElement('img');
      img.src = event.logo?.url || 'https://via.placeholder.com/800x450?text=No+Image';
      img.alt = safeText(event.name?.text) || 'Event image';
      media.appendChild(img);
      
      const content = document.createElement('div');
      content.className = 'content';
      
      const badge = document.createElement('span');
      badge.className = 'event-badge';
      badge.textContent = '🔴 Live Event';
      
      const title = document.createElement('h3');
      title.textContent = safeText(event.name?.text) || 'Untitled event';
      
      const desc = document.createElement('p');
      const raw = event.description?.text || '';
      desc.textContent = raw.length > 260 ? raw.slice(0, 260) + '...' : raw;
      
      const link = document.createElement('a');
      link.className = 'learn-more';
      link.href = event.url || '#';
      link.target = '_blank';
      link.innerHTML = 'Learn More <i class="fa-solid fa-arrow-right"></i>';
      
      content.append(badge, title, desc, link);
      card.append(media, content);
      return card;
    }

    function createCarouselItem(event) {
      const item = document.createElement('a');
      item.className = 'carousel-item';
      item.href = event.url || '#';
      item.target = "_blank";
      
      const img = document.createElement('img');
      img.src = event.logo?.url || 'https://via.placeholder.com/600x400?text=No+Image';
      img.alt = safeText(event.name?.text) || 'Event image';
      
      const meta = document.createElement('div');
      meta.className = 'meta';
      
      const title = document.createElement('h4');
      title.textContent = safeText(event.name?.text) || 'Untitled event';
      
      const dateInfo = document.createElement('p');
      dateInfo.className = 'date-info';
      try {
        const eventDate = event.start?.local ? new Date(event.start.local).toDateString() : '';
        dateInfo.innerHTML = `<i class="fa-regular fa-calendar"></i> ${eventDate}`;
      } catch(e) {
        dateInfo.innerHTML = '<i class="fa-regular fa-calendar"></i> Date unavailable';
      }
      
      meta.append(title, dateInfo);
      item.append(img, meta);
      return item;
    }

    async function fetchAllPastEvents(proxyUrl) {
      let allEvents = [];
      let continuation = '';
      let hasMore = true;

      while(hasMore) {
        const url = continuation 
          ? `${proxyUrl}&status=ended&continuation=${encodeURIComponent(continuation)}`
          : `${proxyUrl}&status=ended`;
        
        const res = await fetch(url);
        
        if (!res.ok) {
          throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        
        if(Array.isArray(data.events)) {
          allEvents = allEvents.concat(data.events);
        }
        
        if(data.pagination && data.pagination.has_more_items && data.pagination.continuation) {
          continuation = data.pagination.continuation;
          hasMore = true;
        } else {
          hasMore = false;
        }
      }

      return allEvents;
    }

    async function fetchEvents() {
      // Self-referencing proxy URL
    //   const PROXY_URL = "?proxy=eventbrite";
    const PROXY_URL = '/digidirect-events/api/events';

      const list = document.getElementById('event-list');
      const carousel = document.getElementById('past-carousel');

      try {
        // Fetch live events
        const liveRes = await fetch(`${PROXY_URL}&status=live`);
        
        if (!liveRes.ok) {
          throw new Error(`HTTP error! status: ${liveRes.status}`);
        }
        
        const liveData = await liveRes.json();
        const liveEvents = Array.isArray(liveData.events) ? liveData.events : [];
        
        list.innerHTML = "";

        if(liveEvents.length) {
          liveEvents.slice(0, 5).forEach(ev => list.appendChild(createCard(ev)));
        } else {
          list.innerHTML = '<p class="muted">No live events found at the moment. Check back soon!</p>';
        }

        // Fetch past events (all pages)
        carousel.innerHTML = '<div class="loading-spinner"><div class="spinner"></div><p class="muted">Loading all past events...</p></div>';
        
        const pastEvents = await fetchAllPastEvents(PROXY_URL);
        carousel.innerHTML = "";

        if(pastEvents.length) {
          pastEvents.sort((a, b) => new Date(b.end.local) - new Date(a.end.local));
          pastEvents.forEach(ev => carousel.appendChild(createCarouselItem(ev)));
        } else {
          carousel.innerHTML = '<p class="muted">No past events found.</p>';
        }

        initCarouselControls();
      } catch(e) {
        console.error("Error fetching events:", e);
        list.innerHTML = '<p class="muted">⚠️ Error loading events. Please refresh the page or try again later.</p>';
        carousel.innerHTML = '<p class="muted">⚠️ Error loading past events. Please refresh the page or try again later.</p>';
      }
    }

    function initCarouselControls() {
      const track = document.getElementById('past-carousel');
      const prevBtn = document.getElementById('prevBtn');
      const nextBtn = document.getElementById('nextBtn');

      const scrollStep = 340;

      nextBtn.addEventListener('click', () => {
        track.scrollBy({ left: scrollStep, behavior: 'smooth' });
      });
      
      prevBtn.addEventListener('click', () => {
        track.scrollBy({ left: -scrollStep, behavior: 'smooth' });
      });

      // Drag & Swipe support
      let isDown = false;
      let startX;
      let scrollLeft;

      track.addEventListener('mousedown', (e) => {
        isDown = true;
        startX = e.pageX - track.offsetLeft;
        scrollLeft = track.scrollLeft;
      });
      
      track.addEventListener('mouseleave', () => { isDown = false; });
      track.addEventListener('mouseup', () => { isDown = false; });
      
      track.addEventListener('mousemove', (e) => {
        if(!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 1.5;
        track.scrollLeft = scrollLeft - walk;
      });

      // Touch (mobile)
      let startTouchX = 0;
      track.addEventListener('touchstart', e => { 
        startTouchX = e.touches[0].pageX; 
      });
      
      track.addEventListener('touchmove', e => {
        const touchX = e.touches[0].pageX;
        const diff = startTouchX - touchX;
        track.scrollLeft += diff * 0.8;
        startTouchX = touchX;
      });
    }

    // Initialize on page load
    fetchEvents();