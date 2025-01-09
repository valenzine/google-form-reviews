# Customer Reviews from Google Sheets

## Project Description

This project displays customer reviews fetched from a Google Sheets document and displays them on a web page. The reviews are collected via a Google Forms survey and automatically populated into the Google Sheets document.

## Features

- Fetches reviews from a Google Sheets document
- Displays reviews with the reviewer's name and date
- Converts dates to a localized format

## Setup Instructions

### Prerequisites

- PHP (version 7.4 or higher)
- A web server (e.g., Apache, Nginx)
- Google Sheets API key

### Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/valenzine/google-form-reviews.git
    cd google-form-reviews
    ```

2. Configure the application:
    - Rename `config.sample.php` to `config.php`
    - Fill in the required values in `config.php`:
        ```php
        $sheetId = 'your_google_sheet_id';
        $sheetName = 'your_sheet_name';
        $apiKey = 'your_google_api_key';
        $locale = 'your_locale'; // e.g., 'es_ES.UTF-8'
        ```

3. Start your web server and navigate to the project directory.

### Setting Up Google Forms and Sheets

1. **Create a Google Form:**
    - Go to [Google Forms](https://forms.google.com) and create a new form.
    - Add fields for the reviewer's name, review text, and date.

2. **Link Google Form to Google Sheets:**
    - After creating the form, click on the "Responses" tab.
    - Click on the green Sheets icon to create a new Google Sheets document that will store the responses.

3. **Get Google Sheets ID and Sheet Name:**
    - Open the Google Sheets document created in the previous step.
    - The Sheet ID is the long string in the URL between `/d/` and `/edit`.
    - The Sheet Name is the name of the sheet tab at the bottom (usually "Form Responses 1" by default).

4. **Enable Google Sheets API:**
    - Go to the [Google Cloud Console](https://console.cloud.google.com/).
    - Create a new project or select an existing one.
    - Enable the Google Sheets API for your project.
    - Create API credentials and get your API key.

## Usage

- Access the application through your web browser.
- The reviews will be displayed on the main page.

## Contributing

1. Fork the repository
2. Create a new branch (`git checkout -b feature-branch`)
3. Make your changes
4. Commit your changes (`git commit -m 'Add some feature'`)
5. Push to the branch (`git push origin feature-branch`)
6. Open a pull request

## License

This project is licensed under the GNU General Public License v3.0.

