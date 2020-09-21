
var table;
var idToDelete;
var idToUpdate;
var editMode = false;

function getUserList() {
  $.get('api/users.php', function(response) {
    console.log(response);
    //table.rows.add(response.result);
    table.clear().draw();
    response.result.forEach(row => {
      let btnEdit = `<button class="btn btn-warning btn-sm" title="Edit" data-target='#modalEdit' data-toogle='modal' name="edit" data-id="${row.id}"><i class="material-icons ">edit</i></button>`;
      let btnDelete = `<button class="btn btn-danger btn-sm" title="Delete" data-target='#modalDelete' data-toogle='modal' name="delete" data-id="${row.id}"><i class="material-icons">delete</i></button>`;
      // var options = '<button class="btn btn-warning btn-icon btn-circle btn-lg" data-target="#modal-distributor" data-toggle="modal" title="Editar Beneficiario" name="edit" data-id="'+row.id+'"> <i class="fas fa-pencil-alt"></i> </button>' +
      //           '<button class="btn btn-danger btn-icon btn-circle btn-lg" title="Eliminar Beneficiario" name="delete" data-id="'+row.id+'"> <i class="fas fa-trash-alt"></i> </button>';
      let actions = btnEdit + btnDelete;
      table.row.add([
        row.id,
        row.first_name,
        row.last_name,
        row.email,
        actions
      ]).draw();
    });
  }, 'json');
}

function onAddUser() {
  editMode = false;
  clearForm();
  showModalUserForm(true);
}

function addUser() {
  let params = {};
  $("#formUser").serializeArray().forEach(element => {
    params[element.name] = element.value;
  });

  if(params.password != params.password_confirm) {
    showFormMessage(true, 'Password are not equals');
    return;
  }

  console.log(params);

  $.post('api/users.php', params, function(response) {
    console.log(response);
    if(response.success) {
      getUserList();
      showModalUserForm(false);
      clearForm();
    } else {
      showFormMessage(true, response.msg);
    }
  }, 'json');
}

function updateUser() {
  let params = {};
  $("#formUser").serializeArray().forEach(element => {
    params[element.name] = element.value;
  });

  console.log(params);

  $.ajax({
    url: 'api/users.php?user_id='+idToUpdate,
    type: 'PUT',
    data: JSON.stringify(params), 
    dataType: "json",
    contentType: "application/json",
    success: function(response) {
      if(response.success) {
        getUserList();
        showModalUserForm(false);
        clearForm();
      }
      else {
        showFormMessage(true, response.msg);
      }
      
      console.log(response);
    }
  });
}

function deleteUser() {
  $.ajax({
    url: 'api/users.php?user_id='+idToDelete,
    type: 'DELETE',
    success: function(response) {
      getUserList();
      showModalDelete(false);
      console.log(response);
    }
  });
}

function clearForm() {
  editMode = false;
  $("#first_name").val('');
  $("#last_name").val('');
  $("#email").val('');
  $("#password").val('');
  $("#password_confirm").val('');
  $("#idBtnUpdate").html('Add');

  disabledPasswordInputs(false);
  showFormMessage(false, '');
}

function showFormMessage(show, message) {
  if(show) {
    $("#msgForm").removeClass('d-none').html(message);
  }
  else {
    if(!$("#msgForm").hasClass('d-none')) {
      $("#msgForm").addClass('d-none');
    }
  }
}

function disabledPasswordInputs(disabled) {
  $("#password").prop('disabled', disabled);
  $("#password_confirm").prop('disabled', disabled);
}

function showModalUserForm(show) {
  $("#modalUser").modal(show? 'show':'hide');
}

function showModalDelete(show) {
  $("#modalDelete").modal(show? 'show':'hide');
}

function init() {
  
  table = $('#idTableUsersList').DataTable({
    responsive: true,
    pageLength: 5,
    lengthMenu: [5, 10, 20, 50]
  });

  $('#idTableUsersList tbody').on( 'click', 'button', function () {
    var name = $(this).attr('name');
    var id = $(this).attr('data-id');

    if(name == 'edit') {
      console.log("Editar");
      idToUpdate = id;
      editMode = true;

      disabledPasswordInputs(true);

      table.rows().every(function() {
        let data = this.data();
        if(this.data()[0] == id) {
          $("#first_name").val(data[1]);
          $("#last_name").val(data[2]);
          $("#email").val(data[3]);
          //$("#password").val(data[1]);
          //strUser = `${data[1]} ${data[2]}`;
        }
      });

      $("#idBtnUpdate").html('Update');
      showModalUserForm(true);
    }
    else if(name == 'delete') {
      let strUser = '';
      idToDelete = id;
      table.rows().every(function() {
        let data = this.data();
        if(this.data()[0] == id) {
          strUser = `${data[1]} ${data[2]}`;
        }
      });
      $('#nameUserToDelete').html(strUser);
      showModalDelete(true);
    }
  });

  $('#idBtnAdd').click(
    () => {
      onAddUser();
    }
  );

  $("#formUser").submit(
    function(event) {
      event.preventDefault();
      if(editMode) {
        updateUser();
      } else {
        addUser();
      }
    }
  );
  
  $("#idBtnDelete").click(
    () => {
      deleteUser();
    }
  );

  $('.modal').on('hidden.bs.modal', function (e) {
    clearForm();
  })

  getUserList();
}

$(document).ready(function() {
  init();
});