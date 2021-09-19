Dropzone.autoDiscover=false;
$(document).ready(function() {
    var referenceList = new ReferenceList($('.js-reference-list'));
    initializeDropzone(referenceList);
    var $serviceSelect = $('.js-form-service');
    var $href=$('.js-href');
    var $data_service;
    var $table_links=['www.google.com/','www.youtube.com'];
    var $specificServiceTarget = $('.js-specific-service-target');
    $serviceSelect.on('change', function(e) {
        $.ajax({
            url: $serviceSelect.data('specific-service-url'),
            data: {
                service: $serviceSelect.val()

            },

            success: function (html) {

                if (!html) {
                    $specificServiceTarget.find('select').remove();
                    $specificServiceTarget.addClass('d-none');
                    return;
                }
                $data_service=$serviceSelect.val();
                $specificServiceTarget
                    .html(html)
                    .removeClass('d-none');

            }
        });
    });
    $specificServiceTarget.change(function(){


               var $data=$('#helo_noma option:selected').text();
               if($data!=null){
                    if($data_service==='names'){
                   $href.attr({
                       "href" : $table_links[0] +$data,
                       "title" :$data
                   })}
                    else if ($data_service==='star'){
                        $href.attr({
                            "href" : $table_links[1] +$data,
                            "title" :$data
                        })}
                    }


               else
               {
                   $href.addClass('d-none');
               }



    });
});
class ReferenceList
{
    constructor($element) {
        this.$element = $element;
        this.references = [];
        this.render();
        this.$element.on('click', '.js-reference-delete', (event) => {
            this.handleReferenceDelete(event);
        });
        $.ajax({
            url: this.$element.data('url')
        }).then(data => {
            this.references = data;
            this.render();
        })
    }

    addReference(reference) {
        this.references.push(reference);
        this.render();
    }
    handleReferenceDelete(event) {
        const $li = $(event.currentTarget).closest('.list-group-item');
        const id = $li.data('id');
        $li.addClass('disabled');

        $.ajax({
            url: '/questions/shows/references/'+id,
            method: 'DELETE'
        }).then(() => {
            this.references = this.references.filter(reference => {
                return reference.id !== id;
            });
            this.render();
        });
    }


    render() {
        const itemsHtml = this.references.map(reference => {
            return `
<li class="list-group-item d-flex justify-content-between align-items-center mb-1" data-id="${reference.id}">
    <input type="text" value="${reference.originalFilename}" class="form-control js-edit-filename" style="width: auto;" >

    <span>
        <a href="/questions/shows/references/${reference.id}/download" class="btn btn-link btn-sm"><img src="https://img.icons8.com/nolan/25/downloading-updates.png"/></a>
        <button class="js-reference-delete btn btn-link btn-sm"><img src="https://img.icons8.com/nolan/25/delete.png"/></button>
    </span>
</li>
`
        });

        this.$element.html(itemsHtml.join(''));
    }
}
/**
 * @param {ReferenceList} referenceList
 */
function initializeDropzone(referenceList) {
    var formElement = document.querySelector('.js-reference-dropzone');
    if (!formElement) {
        return;
    }
    var dropzone = new Dropzone(formElement, {
        paramName: 'reference',
        init:function (){
            this.on('success',function (file,data)
            {
               referenceList.addReference(data);
              const empty= setTimeout(()=>{dropzone.removeAllFiles()},3000);
            })
            this.on('error',function (file,data){
                if(data.detail)
                {
                    this.emit('error',file,data.detail)
                }
            })
        }
    });

}

