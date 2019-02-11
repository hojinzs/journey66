<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Journey 66 Language Lines - en
    |--------------------------------------------------------------------------
    |
    |
    */

    'home' => [
        'slogan' => '멋진 여행기를 작성하고 공유하세요.',
        'write_desc' => '새 여행기 작성',
        'shuffle_desc' => '아무 여행기나 읽기',
    ],
    'form' => [
        'getPath' => [
            'title' => '경로 가져오기',
            'description' => '새 여행기를 작성합니다.',
            'envnotice' => '여행기 작성은 데스크탑 웹 브라우저에 최적화 되어있습니다.',
            'gpx' => [
                'title' => 'gpx 사용',
                'description' => '경로를 gpx파일에서 가져옵니다.',
            ],
            'strava' => [
                'title' => 'strava 연동',
                'description' => '개발중입니다.'
            ],
        ],
        'title' => '제목',
        'style' => '여행 스타일',
        'description' => '소개',
        'description_ph' => '여행에 대한 짧은 소개글',
        'confirm_title' => '여행기 검토 및 발행',
        'author' => '필명',
        'author_ph' => '누가 이 멋진 여행기를 작성했는지 알려주세요',
        'savedAt' => '작성일시',
        'email' => '이메일 주소',
        'email_check' => '작성한 여행기의 수정/삭제를 위한 이메일 수집에 동의합니다. 광고나 메일링에 사용되지 않습니다.',
        'delete' => '여행기 삭제',
        'created_at' => '작성일',
        'inform' => [
            'setmarker' => '경로상에서 기억에 남는 장소를 선택해주세요',
            'geotag_photo' => '아니면.. 위치정보가 포함된 사진을 업로드 해보세요',
            'sort' => '클릭한 지점은 트랙 타임라인 순으로 정렬됩니다.',
        ],
        'stat' => '기록',
        'stats' => [
            'distance' => '총 이동거리',
            'elevation' => '총 상승고도',
            'duration' => '소요시간',
            'startedAt' => '시작일시',
            'finishedAt' => '종료일시',
        ],
        'waypoint' => [
            'waypoint' => '장소',
            'location' => '위치',
            'type' => '타입',
            'photo' => '사진',
            'name' => '주제',
            'description' => '내용',
            'btn_up' => '위로',
            'btn_down' => '아래로',
            'btn_delete' => '삭제',
            'btn_undelete' => '삭제 취소',
            'stat' => '기록',
            'stats' => [
                'distance' => '이동거리',
                'elevation' => '고도',
                'time' => '시각',
            ],
        ],
        'posted' => [
            'title' => '여행기 업로드중',
            'ing' => '서버에 여행기를 업로드 하고 있습니다.',
            'done1' => '여행기 검토 링크를 포함한 이메일을 발송해드렸습니다!',
            'done2' => '여행기가 정상적으로 저장되었으나, 아직 공개된 상태는 아닙니다.',
            'done3' => '이메일을 확인해주시고, 공개 상태로 전환해주세요.',
        ],
        'edited' => [
            'title' => 'Edit your journey',
            'ing' => 'saving on server',
            'done1' => 'Your edits were successfully saved!',
            'gojourney' => 'read your journey',
        ],
        'publish' => '공개설정',
        'published_stages' => [
            'pending' => '대기',
            'pending_description' => '공개 되기 전 검토중입니다.',
            'published' => '공개',
            'published_description' => '웹상에 여행기를 공개합니다.',
            'private' => '비공개',
            'private_description' => '비공개로 전환합니다.',
        ],
        'coverset' => [
            'coverset' => '커버저장',
        ],
        'submit' => '저장하기',
    ],
    'label' => [
        'journey' => [
            'cycling' => '자전거라이딩',
            'mtb' => '산악자전거',
            'motorbike' => '모터싸이클',
            'smart mobi' => '스마트모빌리티',
            'hiking' => '하이킹'
        ],
        'waypoint' => [
            'starting' => '시작',
            'marker' => '기억에 남는 지점', //unuse
            'landmark' => '볼거리',
            'restaurant' => '음식집',
            'supplypoint' => '보급장소',
            'rest' => '휴식장소',
            'event' => '이벤트',
            'accident' => '사건사고',
            'destination' => '종료',
            'milestone' => '이정표',
        ],
    ],
    'email' => [
        'saved' => '여행기가 저장되었습니다.',
        'notice' => '안내',
        'notice_message' => '본 이메일을 소중하게 간직해주세요.',
        'notice_message2' => '이 이메일을 통해 여행기를 수정하거나 저작권을 주장할 수 있습니다.',
        'status_message1' => '여행기가 성공적으로 저장되었으나, 아직 공개된 상태가 아닙니다.',
        'status_message2' => '여행기를 검토한 후 많은 사람들이 볼 수 있게 해주세요.',
        'link' => '수정 및 공개상태 변경하기',
        'share_link_message' => '이 링크를 사용해 여행기를 공유하세요!',
    ]

];