// CMS content loader — reemplaza contenido si la API responde, sino muestra el hardcodeado
(async function () {
  const BASE = 'php-api/';

  // Lanzamientos
  try {
    const res = await fetch(BASE + 'lanzamientos.php');
    const json = await res.json();
    if (json.ok && json.data.length) {
      const grid = document.getElementById('lanzGrid');
      if (grid) {
        grid.innerHTML = json.data.map(item => `
          <div class="lanz-item">
            <div class="lanz-video-wrap">
              <iframe src="https://www.youtube.com/embed/${item.youtube_id}"
                title="${item.titulo}" frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen loading="lazy"></iframe>
            </div>
            <div class="lanz-meta">
              <span class="lanz-artist">${item.artista}</span>
              <p class="lanz-name">${item.titulo}</p>
            </div>
          </div>`).join('');
      }
    }
  } catch (e) { /* fallback al HTML hardcodeado */ }

  // Artistas
  try {
    const res = await fetch(BASE + 'artistas.php');
    const json = await res.json();
    if (json.ok && json.data.length) {
      const newData = {};
      json.data.forEach(a => {
        newData[a.slug] = {
          num:        a.num || '',
          tags:       a.tags ? a.tags.split(',').map(t => t.trim()) : [],
          logo:       a.logo || '',
          photo:      a.foto || '',
          photoPos:   a.photo_pos || 'center top',
          photoScale: a.photo_scale || '',
          desc:       a.bio || '',
          spotify:    a.spotify_embed || '',
          ig:         a.link_instagram || '',
          tk:         a.link_tiktok || '',
          yt:         a.link_youtube || '',
          sp:         a.link_spotify || '',
          g1:         a.color_g1 || '#8B5CF6',
          g2:         a.color_g2 || '#60A5FA',
        };
      });
      window.artistData = newData;

      const grid = document.querySelector('.artists-grid');
      if (grid) {
        grid.innerHTML = json.data.map((a, i) => `
          <div class="artist-card reveal reveal-delay-${i + 1}" data-artist="${a.slug}" style="--g1:${a.color_g1 || '#8B5CF6'};--g2:${a.color_g2 || '#60A5FA'}">
            <div class="artist-card-inner">
              <div class="artist-card-photo-wrap">
                <img src="${a.foto}" class="artist-card-photo" alt="${a.nombre}"
                  ${a.photo_pos ? `style="object-position:${a.photo_pos}${a.photo_scale ? `;transform:scale(${a.photo_scale})` : ''}"` : ''}>
              </div>
              <div class="artist-card-footer">
                <span class="artist-card-name">${a.nombre}</span>
                <span class="artist-card-cta">Conocé más →</span>
              </div>
            </div>
          </div>`).join('');
      }
    }
  } catch (e) { /* fallback al HTML hardcodeado */ }

  // Hero cards
  try {
    const res = await fetch(BASE + 'hero.php');
    const json = await res.json();
    if (json.ok && json.data.length) {
      const wrap = document.getElementById('heroSwipe');
      if (wrap) {
        wrap.innerHTML = json.data.map(item => `
          <div class="hs-card" style="--c:${item.color}">
            <img src="${item.imagen}" alt="${item.alt || item.nombre}">
            <div class="hs-card-bottom">
              <div class="hs-card-icon"></div>
              <div>
                <span>${item.nombre}</span>
                <em>${item.subtitulo || 'Artistas · Babidibu Records'}</em>
              </div>
            </div>
          </div>`).join('');
      }
    }
  } catch (e) { /* fallback al HTML hardcodeado */ }

})();
