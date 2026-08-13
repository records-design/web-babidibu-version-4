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

})();
