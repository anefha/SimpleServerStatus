# Server Status Page

This is a simple server status page implemented in HTML and PHP. It allows you to check the online/offline status of multiple servers and displays their availability in a user-friendly manner.

## Features

- Displays the status of multiple servers
- Provides a visual indicator for online and offline status
- Easy to set up and customize

## Usage

1. Clone or download this repository to your web server.
2. Edit the `index.php` file to configure your server details in the `$servers` array.
3. Access the `index.php` file through your web browser to view the server status page.

## Configuration

Edit the `index.php` file and modify the `$servers` array to include your server information. Each server entry should include the protocol, host IP, and port number.

```php
$servers = array(
    array('protocol' => 'Example', 'host' => '127.0.0.1', 'port' => 80),
    array('protocol' => 'Example2', 'host' => '127.0.0.1', 'port' => 916),
    // Add more server entries as needed
);
