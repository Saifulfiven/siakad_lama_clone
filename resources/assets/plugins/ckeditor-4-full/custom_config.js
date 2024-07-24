    CKEDITOR.editorConfig = function( config ) {
        config.toolbarGroups = [
            { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
            { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
            { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
            { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
            { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
            { name: 'links', groups: [ 'links' ] },
            { name: 'insert', groups: [ 'insert' ] }
        ];

        config.baseFloatZIndex = 1070;
        
        config.removeButtons = 'Source,Save,Templates,Blockquote,Image,Flash,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe,NewPage,Preview,Print,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CopyFormatting,Find,Replace,SelectAll,Scayt,RemoveFormat,CreateDiv,Language,BidiRtl,BidiLtr,Link,Unlink,Anchor';
        
    };