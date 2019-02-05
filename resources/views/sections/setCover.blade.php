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
    console.log(imageArray);
    let Cover = this.Element;
    let Button = Cover.querySelector('button[name=set_cover_btn]');

    //set Slick slider
    console.log("SET SLICK");
    let image_select_list = Cover.querySelector('.image-select-list');
    $(image_select_list).slick({
        infinite: false,
        slidesToShow: 1,
        variableWidth: true,
        arrows: false
    });

    //set Text
    Cover.getElementsByTagName('h2')[0].textContent = CoverData.title;
    Cover.querySelector('span[name=date]').textContent = CoverData.date;
    Cover.querySelector('span[name=distance]').textContent = CoverData.distance;
    Cover.querySelector('span[name=message]').textContent = "Select image and set cover image";
    
    //default data
    let SendForm = new FormData
    SendForm.append('UJID',CoverData.journey);
    SendForm.append('KEY',JourneyKey);

    //set current thumbnail
    let currentImg = new Image;
    currentImg.classList.add("setcover-images");
    currentImg.classList.add('img_selected');
    currentImg.src = CoverData.thumbnail;
    currentImg.dataset.Index = "current";
    currentImg.addEventListener('click',PickThumbnail);
    Cover.style.backgroundImage = "linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("+CoverData.thumbnail+")";
    $(image_select_list).slick('slickAdd',$(currentImg));


    //set Images
    console.log("FOREACH GO, GO, GO");
    imageArray.forEach(image =>{

        //set each image
        let newImg = new Image;
        newImg.classList.add("setcover-images");
        newImg.src = image.path;
        newImg.dataset.Index = image.id;
        newImg.dataset.WaypointId = image.waypoint_id;

        //set click event
        newImg.addEventListener('click',PickThumbnail);

        //inject images to .image-select-list
        $(image_select_list).slick('slickAdd',$(newImg));
    });

    Button.addEventListener('click',function(event){
        setCover._AJAXcall(
        {
            method: "PUT",
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
                // sending message
            },
            SuccessFn(response){
                // success message

                console.log(response);
            },
            ErrorFn(response){
                // error message
                console.log(response);
            },
            CompleteFn(){
                // button reactive
            }
        }
        )
    });

    // finally, show Background
    this._Show(true);

    function PickThumbnail(event){
        console.log(event);

        let target = event.target;

        console.log("CLICK::",target);
        Cover.style.backgroundImage = "linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url("+target.src+")";
        let imgs = Cover.getElementsByClassName('setcover-images');
        for(let i = 0; i < imgs.length; i++){
            let val = imgs[i];
            val.classList.remove('img_selected');
        };
        target.classList.add('img_selected');
        SendForm.append('url',target.src);
        if(target.dataset == "current"){
            Button.disabled = 'disabled';
        } else {
            Button.disabled = false;
        }
    }
};
</script>

<style>
    setCover {
        height: 60vh;
    }

    setCover .setcover-images {
        height: 100px;
        width: auto;
        margin-right: 20px;
    }

    setCover .setcover-images.img_selected{
        border-style: solid;
        border-width: 1px;
        border-color: red;
    }

    setCover .box{
        background-color: white;
        height: auto;
        opacity: .7;
        color: black;
        display: inline-block;
        margin-bottom: 1em;
        padding: 0.8em;
        margin-left: 0.5em;
        box-shadow: 10px 10px 0 0 black;
    }

    setCover .image-select-list{
        margin: 15px 0px 15px 15px;
    }
</style>

<div>
    <div>
        <span name="title" class="box">
            <h2></h2>
        </span>
    </div>

    <div>
        <span name="date" class="box">

        </span>
    </div>

    <div>
        <span name="distance" class="box">

        </span>
    </div>

    <div>
        <span name="message" class="box">
            
        </span>
    </div>

    <div>
        <div class="image-select-list">
            
        </div>
    </div>

    <div>
        <button name="set_cover_btn" type="button" class="btn btn-primary">Cover Set</button>
    </div>
</div>