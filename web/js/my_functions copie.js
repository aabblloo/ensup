$(function () {
    /*
    $(".chosen-select").chosen(
        {
            allow_single_deselect: true,
            no_results_text: "Option non trouvée !",
            disable_search_threshold: 10,
            width: "100%",
            inherit_select_classes: true,
            max_shown_results: 30
        }
    );
    //*/

    $("#table-sort").DataTable({
        "info": true,
        "paging": true,
        "searching": true,
        "lengthMenu": [[30, 50, 100, -1], [30, 50, 100, "All"]],
        "language": {
            "lengthMenu": "Afficher _MENU_ enregistrement par page",
            "zeroRecords": "Pas d'engregistrelent trouvé",
            "info": "Affichage page _PAGE_ sur _PAGES_",
            "infoEmpty": "Pas d'enregistrement",
            "infoFiltered": "(filtré à partir de _MAX_ enregistrements au total)",
            "search": "Filtrer"
        },
        "order":[]
    });
});

function readURL(input, chp) {
    var img = chp + "_img";
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var bouton = '<a href="javascript:delURL(\'' + chp + '\')" class="btn btn-link text-danger">Annuler</a>';
            $(img).html('<img src="' + e.target.result + '" alt="Image" class="img-thumbnail">' + bouton);
            //$(img).attr('src', e.target.result);
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function delURL(chp) {
    $(chp).val('');
    var img = chp + "_img";
    $(img).html('');
}
