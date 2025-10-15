   //var lang = (navigator.language) ? navigator.language : navigator.userLanguage;
    function clock() { // We create a new Date object and assign it to a variable called "time".
        var time = new Date(),

            // Access the "getHours" method on the Date object with the dot accessor.
            hours = time.getHours(),

            // Access the "getMinutes" method with the dot accessor.
            minutes = time.getMinutes(),


            seconds = time.getSeconds();

        document.querySelectorAll(".clock")[0].innerHTML = harold(hours) + ":" + harold(minutes) + ":" + harold(seconds);

        function harold(standIn) {
            if (standIn < 10) {
                standIn = '0' + standIn
            }
            return standIn;
        }
    }
    setInterval(clock, 1000);


    var i = sessionStorage.length;
    while (i--) {
        var key = sessionStorage.key(i);
        if (/startcall-/.test(key)) {
            sessionStorage.removeItem(key);
        }
    }


    function guid() {
        function s4() {
            return Math.floor((1 + Math.random()) * 0x10000)
                .toString(16)
                .substring(1);
        }
        return s4() + s4();
    }


    function queuePause(id, state) {
        $.ajax({
            url: "ajam.php",
            method: "POST",
            data: "Action=QueuePause&ActionId=" + guid() + "&Interface=" + id + "&Paused=" + state,
            success: function(data) {
                $("#test").html(data);
            }
        });
    }


    $(function getQueueSummary() {
        $(function() {
            $.ajax({
                type: "POST",
                url: "./rs/ajm/ajam.php",
                data: "Action=QueueSummary&ActionId=" + guid() + "&Queue=",
                success: function(data) {
                    var queuesum = JSON.parse(data);
                    //$("#queuesum").html(data);
                    var theTemplateScript = $("#queuesum-template").html();
                    var theTemplate = Handlebars.compile(theTemplateScript);
                    var context = { Queuesum: queuesum };
                    var theCompiledHtml = theTemplate(context);
                    $(".queuesum-placeholder").html(theCompiledHtml);
                }
            });
        });
        setTimeout(getQueueSummary, 1382);
    });

    $(function getQueueStatus() {
        $(function() {
            $.ajax({
                type: "POST",
                url: "./rs/ajm/ajam.php",
                data: "Action=QueueStatus&ActionId=" + guid() + "&Queue=",
                success: function(data) {
                    var queue = JSON.parse(data);
                    //$("#rqueues").html(data);
                    var theTemplateScript = $("#queues-template").html();
                    var theTemplate = Handlebars.compile(theTemplateScript);
                    var context = { Queues: queue };
                    var theCompiledHtml = theTemplate(context);
                    $(".queues-placeholder").html(theCompiledHtml);
                }
            });
        });
        setTimeout(getQueueStatus, 999);
    });

    Handlebars.registerHelper("ifE", function(conditional, options) {
        if (conditional == options.hash.equals) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });


    Handlebars.registerHelper("ifQ", function(conditional, options) {
        if (conditional == options.hash.equals) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    Handlebars.registerHelper("ifM", function(conditional, options) {
        if (conditional == options.hash.equals) {
            return options.fn(this);
        } else {
            return options.inverse(this);
        }
    });

    Handlebars.registerHelper("qmStatus", function(status) {
        var lang = (navigator.language) ? navigator.language : navigator.userLanguage;
        var s = status;
        switch (s) {
            case '0':
                var mStatus = "Unknown";
                break;
            case '1':
                var mStatus = '<span class="text-info">' + window.locale[lang]["sts"]["1"] + '</span>';
                break;
            case '2':
                var mStatus = '<span class="text-success">' + window.locale[lang]["sts"]["2"] + '</span>';
                break;
            case '3':
                var mStatus = '<span class="text-danger">' + window.locale[lang]["sts"]["3"] + '</span>';
                break;
            case '4':
                var mStatus = '<span class="text-danger">' + window.locale[lang]["sts"]["4"] + '</span>';
                break;
            case '5':
                var mStatus = '<span class="text-muted">' + window.locale[lang]["sts"]["5"] + '</span>';
                break;
            case '6':
                var mStatus = '<span class="text-danger">' + window.locale[lang]["sts"]["6"] + '</span>';
                break;
            case '7':
                var mStatus = "RINGINUSE";
                break;
            case '8':
                var mStatus = "HOLD";
                break;
        }
        return new Handlebars.SafeString(mStatus);
    });

    Handlebars.registerHelper("qmPaused", function(paused) {
        var lang = (navigator.language) ? navigator.language : navigator.userLanguage;
        var p = paused;
        switch (p) {
            case '0':
                var mPaused = '<span class="text-success">' + window.locale[lang]["sts"]["InQueue"] + '</span>';
                break;
            case '1':
                var mPaused = '<span class="text-warning">' + window.locale[lang]["sts"]["InPaused"] + '</span>';

        }
        return new Handlebars.SafeString(mPaused);
    });

    function ifLen(v) {
        if (v < 10) {
            return '0' + v;
        } else {
            return v;
        }
    }


    Handlebars.registerHelper("lastCall", function(lastcall, status, paused, agent) {

        if (status == '1' && paused !== '1') {
            var dateFromAPI = lastcall;

            var now = new Date();
            var nowTimeStamp = now.getTime() / 1000;
            localStorage.removeItem('startcall-' + agent);
            localStorage.setItem('startcall-' + agent, nowTimeStamp);

            var microSecondsDiff = nowTimeStamp - dateFromAPI;
            var date = new Date(microSecondsDiff * 1000);
            var year = date.getFullYear();
            var month = date.getMonth();
            var day = date.getDay();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var seconds = date.getSeconds();

            return new Handlebars.SafeString("<span class='text-info'>" + ifLen(minute) + ":" + ifLen(seconds) + "</span>");
        } else if (status == '2' || status == '6') {
            var dateFromAPI = lastcall;

            var now = new Date();
            var nowTimeStamp = now.getTime() / 1000;
            if (localStorage.getItem('startcall-' + agent)) {
                var startCall = localStorage.getItem('startcall-' + agent);
            } else {
                var startCall = lastcall
            }
            var microSecondsDiff = nowTimeStamp - startCall;
            var date = new Date(microSecondsDiff * 1000);
            var year = date.getFullYear();
            var month = date.getMonth();
            var day = date.getDay();
            var hour = date.getHours();
            var minute = date.getMinutes();
            var seconds = date.getSeconds();

            // Number of milliseconds per day =
            //   24 hrs/day * 60 minutes/hour * 60 seconds/minute * 1000 msecs/second
            //var daysDiff = Math.floor(microSecondsDiff / (1000 * 60 * 60 * 24));
            if (status == '2') {
                if (minute < 1) {
                    return new Handlebars.SafeString("<span class='text-success'>" + ifLen(minute) + ":" + ifLen(seconds) + "</span>");
                } else if (minute >= 1) {
                    return new Handlebars.SafeString("<span class='text-success'><b>" + ifLen(minute) + ":" + ifLen(seconds) + "</b></span>");
                } else if (minute >= 3) {
                    return new Handlebars.SafeString("<span class='text-warning'><b>" + ifLen(minute) + ":" + ifLen(seconds) + "</b></span>");
                } else {
                    return new Handlebars.SafeString("<span class='text-success'>" + ifLen(minute) + ":" + ifLen(seconds) + "</span>");
                }
            } else if (status == '6') {
                return new Handlebars.SafeString("<span class='text-warning'>" + ifLen(minute) + ":" + ifLen(seconds) + "</span>");
            }
        } else {
            return new Handlebars.SafeString('<span class="text-muted">&nbsp;--:--</span>');
        }
    });
