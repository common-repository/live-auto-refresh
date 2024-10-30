(function() {
	const params = new Proxy(new URLSearchParams(window.location.search), {
		get: (searchParams, prop) => searchParams.get(prop),
	});
	if(params.perron_auto_refresh_status){
		document.location.href=document.location.pathname;
	}

    var lastHash = '';
    var status = parseInt(autoRefresh.status);
    var lastChangeTime = Date.now();
    var postModifiedTime = parseInt(autoRefresh.postModifiedTime);
    if (status) {
		console.info('%c*** LIVE AUTO REFRESH is monitoring for file changes ***', 'color:black;background:yellow;');
        var intervalId = setInterval(function() {
            if (Date.now() - lastChangeTime > 10 * 60 * 1000) {
                clearInterval(intervalId);
				var toolbarbutton = document.getElementById("wp-admin-bar-autorefresh");
				if (toolbarbutton){
					toolbarbutton.className = "autorefreshbuttonpaused";
					toolbarbutton.getElementsByClassName('ab-item')[0].removeAttribute('onclick');
					toolbarbutton.getElementsByClassName('ab-item')[0].href = document.location.pathname;
				}
				console.log('%c*** LIVE AUTO REFRESH has stopped monitoring after 10 minute timeout. Reload to restart monitoring ***', 'color:black;background:orange;');
                return;
            }
			

			
            var data = { action: 'auto_refresh' };
            fetch(autoRefresh.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: Object.keys(data).map(key => key + '=' + data[key]).join('&')
            })
            .then(response => response.text())
            .then(response => {
                response = JSON.parse(response);
				//console.log(response);
				
				if (postModifiedTime && postModifiedTime !== parseInt(response.postModifiedTime)) {
					console.warn('%c*** LIVE AUTO REFRESH detected a save! ***', 'color:white;background:green;');
					clearInterval(intervalId);
					postModifiedTime = parseInt(response.postModifiedTime);
					location.reload();
				}
				
				if (lastHash && lastHash !== response.hash) {
                    if (response.changedFile.endsWith('.css')) {
						console.warn('%c*** LIVE AUTO REFRESH detected a style change! ***', 'color:white;background:green;');
                        reloadStylesheets();
                    } else {
						console.warn('%c*** LIVE AUTO REFRESH detected a file change! ***', 'color:white;background:green;');
                        location.reload();
                    }
                    lastChangeTime = Date.now();
					postModifiedTime = parseInt(response.postModifiedTime);
                }
                lastHash = response.hash;
            });
        }, 1234);
    }else{
		console.info('%c*** LIVE AUTO REFRESH is disabled ***', 'color:white;background:red;');
	}
})();

function reloadStylesheets() {
    var links = document.getElementsByTagName("link");
    for (var i = 0; i < links.length; i++) {
        var link = links[i];
        if (link.rel === "stylesheet") {
            link.href += "?";
        }
    }
}