function getPortraitMetrics($portrait) {
    return {
        leftX: $portrait.offset().left,
        topY: $portrait.offset().top,
        width: $portrait.outerWidth(), // Use .outerWidth() to include padding/border
        height: $portrait.outerHeight(), // Use .outerHeight() to include padding/border
    }
}
