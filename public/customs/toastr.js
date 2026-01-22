$(document).ready(function () {
    // Create an instance of Notyf
    const notyf = new Notyf();

    if (dangerMessage) {
      notyf
      .error({
        message: contentMessage,
        dismissible: true,
        duration: 6000,
        position: {
          x: 'right',
          y: 'top',
        },
      })
      .on('dismiss', ({target, event}) => foobar.retry())
    }

    if (successMessage) {
      notyf
      .success({
        message: contentMessage,
        dismissible: true,
        duration: 6000,
        position: {
          x: 'right',
          y: 'top',
        },
      })
      .on('dismiss', ({target, event}) => foobar.retry())
    }
});
