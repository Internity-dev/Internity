export function useDeleteButton({ buttonId }) {
    window
        .swal({
            title: "Apakah anda yakin?",
            text: "Data yang sudah dihapus tidak dapat dikembalikan!",
            icon: "warning",
            buttons: {
                cancel: {
                    text: "Batal",
                    value: null,
                    visible: true,
                    className: "btn btn-primary",
                    closeModal: true,
                },
                confirm: {
                    text: "Hapus",
                    value: true,
                    visible: true,
                    className: "btn btn-danger",
                    closeModal: true,
                },
            },
        })
        .then((value) => {
            if (value) {
                $(`#${buttonId}`).closest("form").trigger('submit');
            }
        });
}
