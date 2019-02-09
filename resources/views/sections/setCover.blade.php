{{-- MODULE SCRIPT  --}}
{{-- :::HELPER FUNCTION:::
---- => const {name} = new Journey66.Section66
---- => {name}.Element : Element of the section
---- => {name}._AJAXcall ( data{method,url,data}, callback{ BeforeSendFn(), CompleteFn(), SuccessFn(response), ErrorFn(response) })
---- => {name}._Show(boolen) // toggle visibility of the Section
--}}

<script>
document.addEventListener("DOMContentLoaded", function(){
    setCover._Show(false);
});

setCover.Start = function(imageArray,CoverData,JourneyKey){
    let Cover = this.Element;
    let Button = Cover.querySelector('button[name=set_cover_btn]');
    let MessageBox = Cover.querySelector('span[name=message]');
    let PreviewBtn = Cover.querySelector('button[name=journey_preview]');

    //set Slick slider
    let image_select_list = Cover.querySelector('.journey-image-list');
    $(image_select_list).slick({
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        arrows: false
    });


    // set Preview Button
    PreviewBtn.dataset.href = '/journey/'+CoverData.journey+"?key="+JourneyKey
    PreviewBtn.addEventListener('click',function(event){
        location.href = event.target.dataset.href;
    })

    //set Text
    Cover.getElementsByTagName('h2')[0].textContent = CoverData.title;
    Cover.querySelector('span[name=date]').textContent = CoverData.date;
    Cover.querySelector('span[name=distance]').textContent = CoverData.distance;
    MessageBox.textContent = "select and set cover image";
    
    //default data
    let SendForm = new FormData
    SendForm.append('UJID',CoverData.journey);
    SendForm.append('KEY',JourneyKey);

    //set current thumbnail
    let currentImg = new Image;
    currentImg.classList.add("journey-image");
    currentImg.classList.add('img_selected');
    currentImg.classList.add("current_cover");
    currentImg.src = CoverData.thumbnail;
    currentImg.dataset.Index = "current";
    currentImg.addEventListener('click',PickThumbnail);
    Cover.style.backgroundImage = "linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("+CoverData.thumbnail+")";
    $(image_select_list).slick('slickAdd',$(currentImg));
    Button.disabled = 'disabled';


    //set Images
    imageArray.forEach(image =>{

        //set each image
        let newImg = new Image;
        newImg.classList.add("journey-image");
        newImg.src = image.path;

        //set click event
        newImg.addEventListener('click',PickThumbnail);

        //inject images to .journey-image-list
        $(image_select_list).slick('slickAdd',$(newImg));
    });

    Button.addEventListener('click',SetCoverImage = function(event){
        setCover._AJAXcall(
        {
            method: "POST",
            url: '/api/journey/'+CoverData.journey+'/thumbnail_set',
            data: SendForm,
            header : {
                UJID: CoverData.journey,
                key: JourneyKey,
            }
        },
        {
            BeforeSendFn(){

                // button disactive
                Button.disabled = 'disabled';

                // sending message
                MessageBox.textContent = "setting cover image...";

            },
            SuccessFn(response){

                // success message
                MessageBox.textContent = "cover image saved!";

                // remove img selector & button
                document.getElementById("image-selector").querySelector('h3[class=title]').style.display = "none";
                image_select_list.style.display = "none";
                Button.style.display = "none";
                Button.removeEventListener('click',SetCoverImage);
            },
            ErrorFn(response){
                // error message
                MessageBox.textContent = "Error! something happend";
                Button.disabled = false;
            }
        }
        )
    });

    // finally, show Background
    this._Show(true);

    function PickThumbnail(event){
        let target = event.target;

        Cover.style.backgroundImage = "linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("+target.src+")";
        let imgs = Cover.getElementsByClassName('journey-image');
        for(let i = 0; i < imgs.length; i++){
            let val = imgs[i];
            val.classList.remove('img_selected');
        };
        target.classList.add('img_selected');
        SendForm.append('url',target.src);
        if(target.dataset.Index == "current"){
            Button.disabled = 'disabled';
        } else {
            Button.disabled = false;
        }
    }
};
</script>

<style>
    setCover {
        min-height: 60vh;
    }

    #image-selector{
        margin-top: 1.2em;
        width: 100%;
    }
    #image-selector .journey-image-list{
        margin: 15px 0px 15px 15px;
    }

    #image-selector .journey-image {
        height: 100px;
        width: auto;
        margin-right: 20px;
    }
    #image-selector .journey-image.img_selected{
        border-style: solid;
        border-width: 2px;
        border-color: red;
    }
</style>

<div>
    <div>
        <span name="title" class="lilumi-box shadow">
            <h2></h2>
        </span>
    </div>

    <div>
        <span name="date" class="lilumi-box shadow">

        </span>
    </div>

    <div>
        <span name="distance" class="lilumi-box shadow">

        </span>
    </div>

    <div id="image-selector" class="lilumi-box shadow">
        <div>
            <h3 class="title">Select Cover image</h3>
            <span name="message"></span>
        </div>
        <div class="journey-image-list">
            
        </div>
        <div class="button-group">
            <button name="set_cover_btn" type="button" class="btn btn-primary">Cover Set</button>
        </div>
    </div>
    <button name="journey_preview" type="button" class="btn btn-success">Preview ></button>
</div>