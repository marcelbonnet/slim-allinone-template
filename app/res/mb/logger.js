//require jquery
/*
var logger;//instância da classe Logger para mostrar mensagens na view dentro de um DIV
$(document).ready(function(){
    $("#logger").hide();
    logger = new Logger("logger");
    
});
*/
//#######################################
/*
* Classe para mostrar logs na view, com CSS Bootstrap
* <div id="iddoobjeto"></div>
* logger = new Logger("iddoobjeto");
* logger.l('testando o logger',  Logger.INFO, 100);
* @date: 2015-07-27
* @author: marcelbonnet@gmail.com
*/
function Logger(htmlId){
    this.bar=$('#'+htmlId);
    this.level;
    //javascript não suporta overloading de métodos. A saída é arguments.length (como no php) ou param opts que espera json
    this.l=function(msg, level, time, kill){
    	this.level=level;
        //message stack
    	//if (level !== Logger.ERR)
    	this.bar.append('<div></div>');
    	//else
    		//this.bar.append("<div class=\"alert alert-warning alert-dismissible fade in\" role=alert> <button type=button class=close data-dismiss=alert aria-label=Close><span aria-hidden=true>&times;</span></button> </div>");
        var stack = this.bar.find('> div');
        var $last = $( stack[stack.length - 1] );
        $last.html(msg);

        //não funciona, mas pelo console funciona.
        //if ($.type(kill) === "object" ) { console.log("KILL"); console.log($(kill)); $(kill).hide(500) }

        //this.bar.html(msg);
        this.bar.show();
        switch (level){
            case Logger.INFO :
                //this.bar.removeClass().addClass("alert alert-info");
                $last.removeClass().addClass("alert alert-info");
                break;
            case Logger.WARN :
                //this.bar.removeClass().addClass("alert alert-warning");
                $last.removeClass().addClass("alert alert-warning");
                break;
            case Logger.ERR :
                //this.bar.removeClass().addClass("alert alert-danger");
                $last.removeClass().addClass("alert alert-danger");
                break;
            case Logger.OK :
                //this.bar.removeClass().addClass("alert alert-success");
                $last.removeClass().addClass("alert alert-success");
                break;
            case Logger.DEFAULT :
                //this.bar.removeClass().addClass("alert label-default");
                $last.removeClass().addClass("alert label-default");
            default:
        }
        if ( time > 0 )
            this.fade($last, time);

        return $last;
    };
    
    this.fade=function(element, time){
        //this.bar.delay(time).hide(500);//bug do chrome v28? hide requer algum int para o delay funcionar
    	if(this.level === Logger.ERR){
    		var html = element.html();
        	element.html("<button type=\"button\" style=\"text-align:center\" onclick=\"$(this).parent().hide(1000);\">Fechar</button><hr>");
        	element.append(html);
    	} else
    		element.delay(time).hide(500);//bug do chrome v28? hide requer algum int para o delay funcionar
    };
    this.off=function(){
        this.fade(500);
    };
    
}	//fim classe Logger
/*
    atributos static:
*/
Logger.DEFAULT=1;
Logger.INFO=2;
Logger.WARN=3;
Logger.ERR=4;
Logger.OK=5;
//#######################################
