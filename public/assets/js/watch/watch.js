document.addEventListener("DOMContentLoaded", () => {

    const pathArray = window.location.pathname.split('/');

    if (document.querySelector('.status').value === 'downloading') {
        setInterval(() => {
            getStatus('http://192.168.178.86/download-status/' + pathArray[2])
                .then(data => {
                    if (data.arguments.torrents[0].percentDone !== 1) {
                        console.log(data.arguments.torrents[0].percentDone)
                        const percentage = document.querySelector('.percentage');
                        percentage.innerText = Math.round(data.arguments.torrents[0].percentDone * 100);
                    }
                    else {
                        location.reload();
                    }
                });
        },5500);
    }

    setInterval(() => {
        let currentTime = document.querySelector('.watch-theatre-movie-display').currentTime
        setTime('http://192.168.178.86/time/'+pathArray[2], {"time": currentTime});
    }, 2000)

    async function getStatus(url = '') {
        const response = await fetch(url, {
            method: 'GET',
            cache: 'no-cache',
            credentials: 'same-origin',
        });
        return response.json();
    }

    async function setTime(url = '', data) {
        const response = await fetch(url, {
            method: 'POST',
            cache: 'no-cache',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        return response.json();
    }

    document.querySelector('.delete-movie').addEventListener('click', () => {
        document.querySelector('.modal').setAttribute('id', 'modalDisplay')
    })

    document.querySelector('.watch-delete-cancel').addEventListener('click', () => {
        document.querySelector('.modal').setAttribute('id', 'modalHide')
    })
});
