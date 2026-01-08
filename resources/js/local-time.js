/**
 * Local Time Converter
 * Converts UTC timestamps to the user's local timezone
 *
 * Usage in Blade templates:
 * <span data-utc-time="{{ $timestamp->toIso8601String() }}" data-format="date-time">
 *     {{ $timestamp->format('d M Y H:i') }}
 * </span>
 *
 * Supported formats:
 * - "date-time" (default): "08 Jan 2026 14.30"
 * - "date-only": "08 Jan 2026"
 * - "time-only": "14.30"
 * - "full": "08 Januari 2026 14.30"
 */
document.addEventListener('DOMContentLoaded', function () {
    const elements = document.querySelectorAll('[data-utc-time]');

    elements.forEach((el) => {
        const utcTime = el.dataset.utcTime;
        if (!utcTime) return;

        const format = el.dataset.format || 'date-time';
        const date = new Date(utcTime);

        // Skip if invalid date
        if (isNaN(date.getTime())) return;

        let options = {};
        let formattedText = '';

        switch (format) {
            case 'date-only':
                options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                };
                formattedText = date.toLocaleDateString('id-ID', options);
                break;

            case 'time-only':
                options = {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                };
                formattedText = date.toLocaleTimeString('id-ID', options);
                break;

            case 'full':
                options = {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                };
                formattedText = date.toLocaleString('id-ID', options);
                break;

            case 'date-time':
            default:
                options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false,
                };
                formattedText = date.toLocaleString('id-ID', options);
                break;
        }

        // Clean up the formatted text (remove commas, standardize format)
        formattedText = formattedText.replace(/,/g, '').replace(/\./g, ':').replace(/:(\d{2}):(\d{2})$/, '.$1');

        el.textContent = formattedText;
    });
});
