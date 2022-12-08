@extends('layouts.app')
@section('pageTitle')
    Students Page
@endsection

@section('main')
    <div class="container py-5">
        <p id="success_message"></p>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-title">
                        <h4>Students Data</h4>
                        <a href="" data-bs-toggle="modal" data-bs-target="#addStudentModal"
                            class="btn btn-primary float-end btn-sm">Add Student</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <td>ID</td>
                                    <td>Name</td>
                                    <td>Email</td>
                                    <td>Phone</td>
                                    <td>Course</td>
                                    <td>Edit</td>
                                    <td>Delete</td>
                                </tr>
                            </thead>
                            <tbody id="tblStudents"></tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            
            fetchStudent();

            function fetchStudent() {
                $.ajax({
                    type: "GET",
                    url: "/fetch-student",
                    dataType: "json",
                    success: function(response) {
                        $('#tblStudents').html("");
                        $.each(response.students, function(key, value) {
                            $('#tblStudents')
                                .append(' <tr>\
                                                    <td>' + value.id + '</td>\
                                                    <td>' + value.name + '</td>\
                                                    <td>' + value.email + '</td>\
                                                    <td>' + value.phone + '</td>\
                                                    <td>' + value.course + '</td>\
                                                    <td><button type="button" value="' + value.id + '" class="edit_student btn btn-primary btn-sm">Edit</button></td>\
                                                    <td><button type="button" value="' + value.id + '" class="delete_student btn btn-danger btn-sm">Delete</button></td>\
                                                </tr>');
                        });
                    }
                });
            }

            $(document).on('click', '.add_student', function(e) {
                e.preventDefault();
                var data = {
                    'name': $('.name').val(),
                    'email': $('.email').val(),
                    'phone': $('.phone').val(),
                    'course': $('.course').val(),
                };
                // console.log(data);

                // ajax csrf tocken
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                // ajax
                $.ajax({
                    type: "POST",
                    url: "/students",
                    data: data,
                    dataType: "json",
                    success: function(response) {
                        // console.log(response);
                        if (response.status == 400) {
                            $("#saveForm_errorList").html("");
                            $("#saveForm_errorList").addClass('alert alert-danger');
                            $.each(response.errors, function(key, err_value) {
                                $("#saveForm_errorList").append('<li>' + err_value +
                                    '</li>');
                            });
                        } else {
                            $("#saveForm_errorList").html("");
                            $("#success_message").addClass("alert alert-success");
                            $("#success_message").text(response.message);
                            $("#addStudentModal").modal("hide");
                            $("#addStudentModal").find('input').val('');
                            fetchStudent();
                        }
                    }
                });
            });

            $(document).on('click', '.edit_student', function(e) {
                e.preventDefault();
                var stud_id = $(this).val();
                $('#editStudentModal').modal('show');
                $('#edit_stud_id').val(stud_id);
                $.ajax({
                    type: "GET",
                    url: "edit-student/" + stud_id,
                    success: function(response) {
                        if (response.status == 404) {
                            $('#success_message').html("");
                            $('#success_message').addClass('alert alert-danger')
                            $('#success_message').text(response.message);
                        } else {
                            $('#edit_name').val(response.student.name);
                            $('#edit_email').val(response.student.email);
                            $('#edit_phone').val(response.student.phone);
                            $('#edit_course').val(response.student.course);
                        }
                    }
                });
            });

            $(document).on('click', '.update_student', function(e) {
                e.preventDefault();
                var stud_id = $('#edit_stud_id').val();
                var data = {
                    'name': $('#edit_name').val(),
                    'email': $('#edit_email').val(),
                    'phone': $('#edit_phone').val(),
                    'course': $('#edit_course').val(),
                };

                // ajax csrf tocken
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "POST",
                    url: "update-student/" + stud_id,
                    data: data,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 400) {
                            // errors
                            $("#updateForm_errorList").html("");
                            $("#updateForm_errorList").addClass('alert alert-danger');
                            $.each(response.errors, function(key, err_value) {
                                $("#updateForm_errorList").append('<li>' + err_value +
                                    '</li>');
                            });
                        } else if (response.status == 404) {
                            $("#updateForm_errorList").html("");
                            $("#success_message").addClass("alert alert-success");
                            $("#success_message").text(response.message);
                        } else {
                            $("#updateForm_errorList").html("");
                            $("#success_message").addClass("alert alert-success");
                            $("#success_message").text(response.message);
                            $("#editStudentModal").modal('hide');
                            fetchStudent();
                        }
                    }
                });
            });

            $(document).on('click', '.delete_student', function (e) {
                e.preventDefault();
                var stud_id = $(this).val();
                $('#delete_stud_id').val(stud_id);
                $('#deleteStudentModal').modal('show');

            });
            
            $(document).on('click', '.yes_delete_student', function (e) {
                e.preventDefault();
                var stud_id = $("#delete_stud_id").val();

                // ajax csrf tocken
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    type: "DELETE",
                    url: "delete-student/"+stud_id,
                    success: function (response) {
                        $("#success_message").addClass('alert alert-success');
                        $("#success_message").text(response.message);
                        $('#deleteStudentModal').modal('hide');
                        fetchStudent();
                    }
                });
            });

        });
    </script>
@endsection

<!-- addStudentModal Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">

                    <ul id="saveForm_errorList"></ul>

                    <div class="form-group mb-3">
                        <label for="">Student Name</label>
                        <input type="text" name="" id="" class="form-control name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Email</label>
                        <input type="text" name="" id="" class="form-control email">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Phone</label>
                        <input type="text" name="" id="" class="form-control phone">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Course</label>
                        <input type="text" name="" id="" class="form-control course">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary add_student">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- End addStudentModal Modal -->

<!-- editStudentModal Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">

                    <ul id="updateForm_errorList"></ul>

                    <input type="hidden" id="edit_stud_id" readonly>

                    <div class="form-group mb-3">
                        <label for="">Student Name</label>
                        <input type="text" id="edit_name" class="form-control name">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Email</label>
                        <input type="text" id="edit_email" class="form-control email">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Phone</label>
                        <input type="text" id="edit_phone" class="form-control phone">
                    </div>
                    <div class="form-group mb-3">
                        <label for="">Course</label>
                        <input type="text" id="edit_course" class="form-control course">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary update_student">Update</button>
            </div>
        </div>
    </div>
</div>
<!-- End editStudentModal Modal -->

<!-- deleteStudentModal Modal -->
<div class="modal fade" id="deleteStudentModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Delete Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="" method="POST">
                    <input type="hidden" id="delete_stud_id" readonly>
                    <h4>Are yoy sure ? want to delete this data ?</h4>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary yes_delete_student">Yes Delete</button>
            </div>
        </div>
    </div>
</div>
<!-- End deleteStudentModal Modal -->
