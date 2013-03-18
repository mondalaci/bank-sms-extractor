function restoreCheckboxes()
{
    $(':checkbox').each(function() {
        var utime = $(this).data('utime');
        var isCheckboxSet = localStorage.getItem(utime);
        $(this).prop('checked', isCheckboxSet == 'true');
    })
}

$(function() {
    restoreCheckboxes();

    $(':checkbox').click(function() {
        var utime = $(this).data('utime');
        var isChecked = $(this).is(':checked');
        localStorage.setItem(utime, isChecked);
    });

});
