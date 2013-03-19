function restoreCheckboxes()
{
    $(':checkbox').each(function() {
        var isTransactionSuccessful = $(this).data('success');
        if (isTransactionSuccessful) {
            var utime = $(this).data('utime');
            var isCheckboxSet = localStorage.getItem(utime) == 'true';
            $(this).prop('checked', isCheckboxSet);
        } else {
            $(this).prop('disabled', true);
            $(this).prop('checked', true);
            $(this).closest('tr').addClass('unsuccessful gray');
        }
    });
}

function updateReloadButton()
{
    var last_utime = $(':checkbox:last').data('utime');
    $.ajax('queryNewTransactionCount.php?last_utime=' + last_utime).done(function(newTransactionCount) {
        var isNewTransaction = newTransactionCount > 0;
        var pluralS = newTransactionCount <= 1 ? '' : 's';
        var buttonText = isNewTransaction
            ? 'Display %i new transaction%s'.replace('%i', newTransactionCount).replace('%s', pluralS)
            : 'No new transactions';
        $('#reload').prop('value', buttonText).prop('disabled', !isNewTransaction);
    });
}

function keepReloadButtonUpdated()
{
    var updateDelay = 300 * 1000;  // microseconds

    if (timerId !== null) {
        return;
    }

    timerId = setInterval(function() {
        updateReloadButton();

        if (!shouldUpdateReloadButton()) {
            clearInterval(timerId);
            timerId = null;
        }
    }, updateDelay);
}

function shouldUpdateReloadButton()
{
    return navigator.onLine && document.webkitVisibilityState == 'visible';
}

$(function() {
    restoreCheckboxes();

    $(':checkbox').click(function() {
        var utime = $(this).data('utime');
        var isChecked = $(this).is(':checked');
        localStorage.setItem(utime, isChecked);
    });

    $('input#reload').click(function() {
        location.reload();
    });

    window.addEventListener("online", function() {
        keepReloadButtonUpdated();
    }, false)

    document.addEventListener("webkitvisibilitychange", function() {
        keepReloadButtonUpdated();
    }, false);

    timerId = null;
    updateReloadButton();
    keepReloadButtonUpdated();
});
