'use strict';

document.addEventListener('DOMContentLoaded', () => {

    const tooltips = document.querySelectorAll('.term-tooltip');

    const setTooltipPosition = (element) => {

        /*
         * Alte Ausrichtung entfernen.
         */
        element.removeAttribute('data-tooltip-align');

        const rect = element.getBoundingClientRect();

        /*
         * Maximale Tooltip-Breite aus dem CSS:
         * 340px bzw. Fensterbreite - 40px.
         */
        const tooltipWidth = Math.min(
            340,
            window.innerWidth - 40
        );

        const margin = 20;

        /*
         * Position bei zentrierter Darstellung.
         */
        const center =
            rect.left + (rect.width / 2);

        const tooltipLeft =
            center - (tooltipWidth / 2);

        const tooltipRight =
            center + (tooltipWidth / 2);

        /*
         * Links würde der Tooltip aus dem Fenster laufen.
         */
        if (tooltipLeft < margin) {

            element.setAttribute(
                'data-tooltip-align',
                'left'
            );

            return;
        }

        /*
         * Rechts würde der Tooltip aus dem Fenster laufen.
         */
        if (
            tooltipRight >
            window.innerWidth - margin
        ) {

            element.setAttribute(
                'data-tooltip-align',
                'right'
            );

            return;
        }

        /*
         * Ansonsten bleibt er zentriert.
         */
    };

    tooltips.forEach((element) => {

        element.addEventListener(
            'mouseenter',
            () => setTooltipPosition(element)
        );

        element.addEventListener(
            'focus',
            () => setTooltipPosition(element)
        );

    });

});