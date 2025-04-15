// builder.js : UI drag & drop vanilla JS pour Header/Footer Builder dans le Customizer
(function($){
    wp.customize('lejournaldesactus_header_footer_builder_data', function(value) {
        var control = $('#customize-control-lejournaldesactus_header_footer_builder_data');
        control.empty();
        // Palette de blocs disponibles
        var blocks = [
            { type: 'logo', label: 'Logo', icon: '', config: [] },
            { type: 'menu', label: 'Menu', icon: '', config: [{key:'menu', label:'Menu', type:'select', options:window.lejournaldesactusMenus||[]}] },
            { type: 'button', label: 'Bouton', icon: '', config: [{key:'text', label:'Texte', type:'text'},{key:'url',label:'URL',type:'text'}] },
            { type: 'social', label: 'Rseaux sociaux', icon: '', config: [{key:'links',label:'Liens (sparats par ,)',type:'text'}] },
            { type: 'cta', label: 'CTA', icon: '', config: [{key:'text',label:'Texte',type:'text'},{key:'url',label:'URL',type:'text'}] }
        ];
        // Layouts premium prdfinis
        var layouts = [
            { name:'Logo  gauche, menu  droite', structure:{header:[{type:'logo'},{type:'menu'}],footer:[]} },
            { name:'Logo centr, menu dessous', structure:{header:[{type:'logo'},{type:'menu'}],footer:[]} },
            { name:'Logo, menu, bouton', structure:{header:[{type:'logo'},{type:'menu'},{type:'button'}],footer:[]} },
            { name:'Menu + rseaux sociaux', structure:{header:[{type:'menu'},{type:'social'}],footer:[]} }
        ];
        // Structure initiale (header et footer vides)
        var structure = value.get() ? JSON.parse(value.get()) : {
            header: [],
            footer: []
        };
        // Cration de l'UI
        var html = '' +
            '<div class="builder-ui">' +
                '<div class="builder-blocks"><strong>Blocs disponibles</strong>' +
                    blocks.map(function(b){
                        return '<div class="builder-block" draggable="true" data-type="'+b.type+'">'+b.icon+' '+b.label+'</div>';
                    }).join('') +
                    '<div class="builder-layouts"><strong>Layouts premium</strong>' +
                        layouts.map(function(l,i){
                            return '<button type="button" class="builder-layout" data-layout="'+i+'">'+l.name+'</button>';
                        }).join('') +
                    '</div>' +
                '</div>' +
                '<div class="builder-zones">' +
                    '<div class="builder-zone" data-zone="header"><h4>Header</h4><div class="zone-drop"></div></div>' +
                    '<div class="builder-zone" data-zone="footer"><h4>Footer</h4><div class="zone-drop"></div></div>' +
                '</div>' +
                '<div class="builder-actions"><button type="button" class="builder-reset">Rinitialiser</button></div>' +
                '<div class="builder-config-modal" style="display:none;"></div>' +
            '</div>';
        control.append(html);
        // Remplir les zones avec la structure actuelle
        function renderZones() {
            ['header','footer'].forEach(function(zone) {
                var zoneDrop = control.find('.builder-zone[data-zone="'+zone+'"] .zone-drop');
                zoneDrop.empty();
                structure[zone].forEach(function(b, idx) {
                    var block = blocks.find(function(bb){ return bb.type === b.type; });
                    if(block) {
                        var configIcon = block.config.length ? '' : '';
                        zoneDrop.append('<div class="builder-block builder-block-inzone" draggable="true" data-type="'+block.type+'" data-idx="'+idx+'">'+block.icon+' '+block.label+' <span class="move-block" title="Dplacer"></span> <span class="config-block" title="Configurer">'+configIcon+'</span> <span class="remove-block" title="Supprimer"></span></div>');
                    }
                });
            });
        }
        renderZones();
        // Drag & Drop interne (rordonner)
        var draggedBlock = null;
        var dragOriginZone = null, dragOriginIdx = null;
        control.on('dragstart', '.builder-block-inzone', function(e){
            draggedBlock = $(this).data('type');
            dragOriginZone = $(this).closest('.builder-zone').data('zone');
            dragOriginIdx = $(this).data('idx');
            e.originalEvent.dataTransfer.effectAllowed = 'move';
        });
        control.on('dragover', '.zone-drop', function(e){
            e.preventDefault();
            $(this).addClass('drag-over');
        });
        control.on('dragleave', '.zone-drop', function(e){
            $(this).removeClass('drag-over');
        });
        control.on('drop', '.zone-drop', function(e){
            e.preventDefault();
            $(this).removeClass('drag-over');
            var zone = $(this).closest('.builder-zone').data('zone');
            if(dragOriginZone!==null && dragOriginZone===zone) {
                // Rordonner dans la mme zone
                var blocksArr = structure[zone];
                var moved = blocksArr.splice(dragOriginIdx,1)[0];
                var dropIdx = $(e.target).closest('.builder-block-inzone').data('idx');
                if(typeof dropIdx==='undefined') blocksArr.push(moved);
                else blocksArr.splice(dropIdx,0,moved);
            } else if(typeof draggedBlock === 'string') {
                // Nouveau bloc depuis la palette
                structure[zone].push({type: draggedBlock});
            } else if(dragOriginZone && dragOriginZone!==zone) {
                // Dplacer d'une zone  l'autre
                var moved = structure[dragOriginZone].splice(dragOriginIdx,1)[0];
                structure[zone].push(moved);
            }
            value.set(JSON.stringify(structure));
            renderZones();
            draggedBlock = null; dragOriginZone = null; dragOriginIdx = null;
        });
        // Configuration individuelle d'un bloc
        control.on('click', '.config-block', function(){
            var zone = $(this).closest('.builder-zone').data('zone');
            var idx = $(this).parent().data('idx');
            var block = structure[zone][idx];
            var blockDef = blocks.find(function(bb){ return bb.type === block.type; });
            if(!blockDef || !blockDef.config.length) return;
            var modal = control.find('.builder-config-modal');
            var html = '<div class="builder-modal-content"><h4>Configurer '+blockDef.label+'</h4><form>';
            blockDef.config.forEach(function(cfg){
                var val = block[cfg.key]||'';
                if(cfg.type==='text')
                    html += '<label>'+cfg.label+'<input type="text" name="'+cfg.key+'" value="'+val+'" /></label><br />';
                else if(cfg.type==='select') {
                    html += '<label>'+cfg.label+'<select name="'+cfg.key+'">';
                    (cfg.options||[]).forEach(function(opt){
                        html+='<option value="'+opt.slug+'"'+(val===opt.slug?' selected':'')+'>'+opt.name+'</option>';
                    });
                    html+='</select></label><br />';
                }
            });
            html += '<button type="submit">Valider</button> <button type="button" class="close-modal">Annuler</button></form></div>';
            modal.html(html).show();
            modal.find('form').on('submit',function(e){
                e.preventDefault();
                blockDef.config.forEach(function(cfg){
                    block[cfg.key]=modal.find('[name="'+cfg.key+'"]').val();
                });
                value.set(JSON.stringify(structure));
                renderZones();
                modal.hide();
            });
            modal.find('.close-modal').on('click',function(){ modal.hide(); });
        });
        // Suppression d'un bloc
        control.on('click', '.remove-block', function(){
            var zone = $(this).closest('.builder-zone').data('zone');
            var idx = $(this).parent().data('idx');
            structure[zone].splice(idx,1);
            value.set(JSON.stringify(structure));
            renderZones();
        });
        // Rinitialiser
        control.on('click', '.builder-reset', function(){
            structure = {header:[], footer:[]};
            value.set(JSON.stringify(structure));
            renderZones();
        });
        // Appliquer un layout premium
        control.on('click', '.builder-layout', function(){
            var layout = layouts[$(this).data('layout')];
            if(layout) {
                structure = JSON.parse(JSON.stringify(layout.structure));
                value.set(JSON.stringify(structure));
                renderZones();
            }
        });
        // Synchronisation Customizer → UI
        value.bind(function(newVal){
            structure = newVal ? JSON.parse(newVal) : {header:[], footer:[]};
            renderZones();
        });
    });
    // Style rapide
    var style = '<style>\
    .builder-ui{display:flex;gap:2em;align-items:flex-start;margin-top:1em;}\
    .builder-blocks{min-width:160px;background:#f9f9f9;padding:1em;border-radius:6px;border:1px solid #ddd;}\
    .builder-block{cursor:grab;user-select:none;background:#fff;margin:0.3em 0;padding:0.4em 0.7em;border-radius:4px;border:1px solid #eee;transition:box-shadow .2s;}\
    .builder-block-inzone{background:#e3f0ff;border:1px solid #b6d7ff;position:relative;}\
    .zone-drop{min-height:38px;padding:0.5em;background:#fafbfc;border:1px dashed #bbb;border-radius:4px;min-width:150px;}\
    .drag-over{background:#d6f5e7!important;}\
    .builder-zone{margin-bottom:1em;}\
    .remove-block{float:right;cursor:pointer;color:#c00;margin-left:10px;}\
    .move-block{float:right;cursor:grab;color:#888;margin-left:10px;}\
    .config-block{float:right;cursor:pointer;color:#0073aa;margin-left:10px;}\
    .builder-actions{margin-top:1em;}\
    .builder-layouts{margin-top:1.5em;}\
    .builder-layout{margin:0.2em 0.2em 0 0;padding:0.3em 0.8em;border-radius:4px;background:#e9e9e9;border:1px solid #bbb;cursor:pointer;}\
    .builder-layout:hover{background:#d6f5e7;}\
    .builder-config-modal{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.25);z-index:9999;display:flex;align-items:center;justify-content:center;}\
    .builder-modal-content{background:#fff;padding:2em;border-radius:8px;box-shadow:0 2px 16px #0002;}\
    </style>';
    if(!$('#builder-ui-style').length) $('head').append(style.replace('<style>','<style id="builder-ui-style">'));
    // Injection des menus WordPress pour la config menu (si besoin)
    if(typeof window.lejournaldesactusMenus==='undefined') {
        $.get(ajaxurl+'?action=lejournaldesactus_get_menus',function(data){
            try{window.lejournaldesactusMenus=JSON.parse(data);}catch(e){}
        });
    }
})(jQuery);
