$(function(){
    $(".fold-table tr.view").on("click", function(){
      $(this).toggleClass("open").next(".fold").toggleClass("open");
    });
  });


  
$('#deleteModel').on('show.bs.modal', function(event) {
  var button = $(event.relatedTarget)
  var id = button.data('id')

  var modal = $(this)
  modal.find('.modal-body #id').val(id);

})