"use strict";

function buildTaskText(taskText){
    return '<span class="task-text">'+taskText+'</span>'+
        '<button class="btn btn-info btn-xs edit-task pull-right"><span class="glyphicon glyphicon-pencil"></span> Edit</button>';
}

function buildEditForm(taskText){
    return '<form class="update-task">' +
        '<div class="input-group">'+
            '<input name="task" value="'+taskText+'" class="form-control" />' +
            '<div class="input-group-btn">'+
                '<button class="btn btn-success">Save</button>'+
            '</div>'+
        '</div>'+
    '</form>';
}

function buildTaskItem(task){
    return '<li class="list-group-item task-item" data-id="'+task.id+'">' +
        '<div class="row">' +
            '<div class="col-xs-1">'+
                '<input type="checkbox" class="delete-task pull-left" />' +
            '</div>' +
            '<div class="col-xs-11 task-text-wrapper">'+
                buildTaskText(task.task)+
            '</div>'+
        '</div>'+
    '</li>';
}

(function($){
    //Get current tasks and display them
    $.get('/api/tasks/',function(data){
        if(data.status ==='success'){
            $.each(data.tasks,function(index, task){
                $('#task-list').append(buildTaskItem(task));
            });
        }
    },'json');

    /////////////////
    //Event Handlers
    /////////////////
    $(document).on('create_task',function(event, task){
        $.post('/api/task/create/',{"task":task},function(data){
            if(data.status ==='success'){
                $('#task-list').append(buildTaskItem(data.task));
            }
            else{
                alert(data.message);
            }
        },'json');
    });
    $(document).on('delete_task',function(event, taskId){
        $.ajax({
            url: '/api/task/delete/'+taskId+'/',
            type: 'DELETE',
            dataType: 'json',
            success: function(data){
                if(data.status === 'success'){
                    $('#task-list li[data-id="'+taskId+'"]').remove();
                }
            },
            error: function(jqXHR, statusCode, errMessage){
                alert(errMessage);
            }
        });
    });
    $(document).on('update_task',function(event, taskId, taskText){
        $.ajax({
            url: '/api/task/update/'+taskId+'/',
            type: 'PUT',
            data: {"task":taskText},
            dataType: 'json',
            success: function(data){
                if(data.status === 'success'){
                    $('#task-list li[data-id="'+taskId+'"] .task-text-wrapper').html(buildTaskText(data.task.task));
                }
            },
            error: function(jqXHR, statusCode, errMessage){
                alert(errMessage);
            }
        });
    });
    /////////////////////
    // End Event Handlers
    /////////////////////

    //////////////////////////
    //Event Listeners/Triggers
    //////////////////////////

    //Trigger the task create event when the creat-task for is submitted
    $('form.create-task').on('submit',function(e){
        e.preventDefault();
        $(document).trigger('create_task',[$('[name="task"]',this).val()]);
        $('[name="task"]',this).val("");
    });
    //Delete a task when its checkbox is clicked
    $(document).on('click','.delete-task',function(e){
        e.preventDefault();
        $(document).trigger('delete_task',$(this).closest('li.task-item').data('id'));
    });
    //Insert an update form when the edit button is clicked
    $(document).on('click','.edit-task',function(e){
        $(this).closest('div.task-text-wrapper').html(buildEditForm($(this).siblings('.task-text').text()));
    });
    //Catch the update form submission and trigger the update_task event
    $(document).on('submit','.update-task',function(e){
        e.preventDefault();
        $(document).trigger('update_task',[$(this).closest('li.task-item').data('id'), $('[name="task"]',this).val()]);
    });
    ///////////////////////////////
    // End Event Listeners/Triggers
    ///////////////////////////////

})(jQuery);