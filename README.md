# URL Shortener - duanlian

## Overview

The URL Shortener project is a web application that allows users to shorten long URLs for easier sharing. The application ensures the privacy and anonymity of users by not tracking the original URLs or the shortened URLs.

## Features

-   **URL Shortening**: Convert long URLs into short, shareable links.
-   **Clipboard Copy**: Easily copy the shortened URL to the clipboard with a single click.
-   **Security**: Shortened URLs are stored using argon2id hashing and AES-256 encryption.
-   **Anonymity**: No tracking of original or shortened URLs to ensure user privacy.
-   **Expiration**: Shortened URLs are valid for an hour or until they are viewed, after which they are deleted from the database.

## Components

### UrlShortener.vue

-   **Form**: Users can input their long URLs and submit the form to generate a shortened URL.
-   **Display**: The shortened URL is displayed with a clickable link and a copy-to-clipboard feature.
-   **Icons**: Uses `ClipboardDocumentListIcon` and `CheckCircleIcon` from `@heroicons/vue/24/solid` for visual feedback.

### FAQs.vue

-   **Frequently Asked Questions**: Provides answers to common questions about the URL shortening service, including customization, deletion, tracking, and validity of shortened URLs.

### Welcome.vue

-   **Introduction**: Describes the security and ease of use of the URL shortening service.
-   **FAQs**: Includes the FAQs component for user reference.
-   **Footer**: Displays the footer of the application.

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/yourusername/url-shortener.git
    ```
2. Navigate to the project directory:
    ```sh
    cd url-shortener
    ```
3. Install dependencies:
    ```sh
    npm install
    ```
4. Set up the environment variables by copying `.env.example` to `.env` and configuring as needed.

## Usage

1. Start the development server:
    ```sh
    npm run dev
    ```
2. Open your browser and navigate to `http://localhost:3000` to use the URL Shortener application.

## Testing

Run the tests using PHPUnit:

```sh
vendor/bin/phpunit
```
