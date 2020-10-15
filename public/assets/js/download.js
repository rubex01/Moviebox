setInterval(() => {
    getStatus('http://192.168.178.86/download-status')
        .then(data => {
            data.arguments.torrents.forEach(torrent => {
                if (torrent.percentDone === 1) {
                    const movie = document.querySelector('.movie-'+torrent.hashString);
                    movie.setAttribute('id', 'download-inactive');
                }
            })
        });
},5500);

async function getStatus(url = '') {
    const response = await fetch(url, {
        method: 'GET',
        cache: 'no-cache',
        credentials: 'same-origin',
    });
    return response.json();
}