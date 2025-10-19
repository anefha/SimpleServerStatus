<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Server Status</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/main.css">
</head>

<body>
    <div class="dashboard">
        <div class="header">
            <h1>Server Monitoring</h1>
            <p>Real-time server status overview</p>
        </div>

        <div id="settings-info" class="settings-info">
            <!-- IP Status -->
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value" id="total-count">-</div>
                <div class="stat-label">Total Servers</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="up-count">-</div>
                <div class="stat-label">Online</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" id="down-count">-</div>
                <div class="stat-label">Offline</div>
            </div>
        </div>

        <div class="servers-grid" id="servers-container">
            <div class="loading">
                <i class="fas fa-spinner fa-spin"></i> Checking server status...
            </div>
        </div>

        <div class="last-update" id="last-update">
            Last updated: <span id="update-time">-</span>
        </div>
    </div>

    <script>
        // get server status
        function fetchServerStatus() {
            return fetch('server_status.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('network response was not ok');
                    }
                    return response.json();
                })
                .catch(error => {
                    console.error('error fetching server status:', error);
                    throw error;
                });
        }

        function initializeDashboard() {
            fetchServerStatus().then(data => {
                if (data.error) {
                    showError(data.error);
                } else {
                    updateUI(data);
                }
            }).catch(error => {
                showError('failed to load server status: ' + error.message);
            });

            // check
            setInterval(() => {
                fetchServerStatus().then(data => {
                    if (data.error) {
                        console.error('server status error:', data.error);
                    } else {
                        updateUI(data);
                    }
                }).catch(error => {
                    console.error('failed to refresh server status:', error);
                });
            }, 30000);
        }

        function showError(message) {
            const container = document.getElementById('servers-container');
            container.innerHTML = `<div class="error">${message}</div>`;
        }

        function updateUI(serverStatuses) {
            const container = document.getElementById('servers-container');
            const settingsInfo = document.getElementById('settings-info');
            let upCount = 0;
            let downCount = 0;

            container.innerHTML = '';

            // check if ip hide
            const isIPHidden = serverStatuses.length > 0 &&
                serverStatuses[0].host !== serverStatuses[0].display_host;

            settingsInfo.textContent = isIPHidden
                ? 'IP addresses are hidden'
                : 'IP addresses are visible';

            serverStatuses.forEach(server => {
                const isUp = server.status;
                if (isUp) upCount++;
                else downCount++;

                const serverCard = document.createElement('div');
                serverCard.className = `server-card ${isUp ? '' : 'down'}`;
                const addressDisplay = `${server.display_host}:${server.port}`;

                serverCard.innerHTML = `
                    <div class="server-info">
                        <div class="server-name">${server.name}</div>
                        <div class="server-address">${addressDisplay}</div>
                    </div>
                    <div class="server-status">
                        <div class="status-badge ${isUp ? 'status-up' : 'status-down'}">
                            ${isUp ? 'ONLINE' : 'OFFLINE'}
                        </div>
                        <i class="fas ${isUp ? 'fa-check-circle icon-up' : 'fa-times-circle icon-down'} status-icon"></i>
                    </div>
                `;
                container.appendChild(serverCard);
            });

            document.getElementById('total-count').textContent = serverStatuses.length;
            document.getElementById('up-count').textContent = upCount;
            document.getElementById('down-count').textContent = downCount;
            document.getElementById('update-time').textContent = new Date().toLocaleTimeString();
        }
        document.addEventListener('DOMContentLoaded', initializeDashboard);
    </script>
</body>

</html>