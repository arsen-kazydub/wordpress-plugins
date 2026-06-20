# WhatsApp Click Tracker

A WordPress plugin that tracks WhatsApp button clicks, stores statistics in the database,
and sends weekly email reports.


## Preview

**Reports Page**

<kbd>
  <img src="screenshots/wct-admin-reports.webp" alt="Admin reports page with sortable click statistics tables">
</kbd>

<div>&nbsp;</div>

**Settings Page**

<kbd>
  <img src="screenshots/wct-admin-settings.webp" alt="Admin settings page with CSS selector and report recipients fields">
</kbd>

<div>&nbsp;</div>

**Email Report**

<kbd>
  <img src="screenshots/wct-email-report.webp" alt="Weekly email report containing aggregated WhatsApp click statistics">
</kbd>


## Features

* WhatsApp button click tracking
* Statistics stored in a dedicated DB table
* Sortable admin reports
* Weekly email reports
* Configurable CSS selector and recipients list


## How It Works

* Frontend listener captures WhatsApp button clicks
* AJAX endpoint receives and validates requests
* Click data is stored in the database
* Reports are displayed in the admin panel and sent weekly via email


## Technologies

* PHP
* JavaScript
* MySQL
* jQuery DataTables (admin reports)


## Options

| Option              | Description                                                                                                                                        |
|---------------------|----------------------------------------------------------------------------------------------------------------------------------------------------|
| `CSS selector`      | Any valid CSS selector to target your WhatsApp button(s)                                                                                           |
| `Report recipients` | A list of emails that will receive reports<br />One email per line with no commas<br />`admin_email` can be used to include the site admin address |