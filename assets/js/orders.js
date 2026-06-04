function updateStatus(orderId, action){
    $.post("order_actions.php",
    {order_id:orderId, action:action},
    function(){
        location.reload();
    });
}

$(".acceptBtn").click(function(){
    updateStatus($(this).data("id"), "accept");
});

$(".cancelBtn").click(function(){
    updateStatus($(this).data("id"), "cancel");
});

$(".prepBtn").click(function(){
    updateStatus($(this).data("id"), "prepare");
});

$(".readyBtn").click(function(){
    updateStatus($(this).data("id"), "ready");
});

$(".completeBtn").click(function(){
    updateStatus($(this).data("id"), "complete");
});

$(".viewBtn").click(function(){
    let id = $(this).data("id");
    $.post("order_actions.php",
    {order_id:id, action:"view"},
    function(data){
        $("#modalBody").html(data);
        $("#orderModal").show();
    });
});

$(".close").click(function(){
    $("#orderModal").hide();
});

/* AUTO REFRESH */
setInterval(function(){
    location.reload();
}, 15000);
