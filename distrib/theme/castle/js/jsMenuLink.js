/*
플래쉬 메뉴 링크

메인페이지				0,0,0
계정비밀번호분실        0,1,0
이용약관                0,2,0
개인정보취급방침        0,3,0
사이트맵                0,4,0

[소식]
공지사항                1,1,0
업데이트                1,2,0
이벤트                  1,3,0
칼럼                    1,4,0
페이퍼진                1,5,0


[가이드]
게임소개                2,1,0
초보자가이드            2,2,0
게임 시스템             2,3,0
퀘스트                  2,4,0
테마가이드              2,5,0

[백과사전]
캐릭터                  3,1,0
스킬                    3,2,0
아이템                  3,3,0
지역                    3,4,0
몬스터                  3,5,0
NPC                     3,6,0

[뮤티즌광장]
자유게시판              4,1,0
아이템장터              4,2,0
묻고답하기              4,3,0
스크린샷                4,4,0
리플달기                4,5,0
설문조사                4,6,0

[자료실]
클라이언트              5,1,0
드라이버                5,2,0
동영상                  5,3,0
월페이퍼                5,4,0



[상점]
상점                    6,1,0
보관함                  6,2,0
프리미엄 쿠폰함         6,3,0
내 캐시                 6,4,0


[내정보]
내 정보 메인            7,1,0
개인정보변경            7,2,0
소개정보변경            7,3,0
비밀번호변경            7,4,0
질문답변변경            7,5,0
쪽지함                  7,6,0
뮤 알리미 관리          7,7,0
개인정보삭제신청        7,8,0
본인인증                7,9,0
닉네임등록              7,10,0


[고객지원]
이용안내                8,1,0
FAQ                     8,2,0
통합신고                8,3,0
통합문의                8,4,0
버그제보                8,5,0
내 문의 내역            8,6,0
운영정책                8,7,0
불량이용자명단          8,8,0
신고취하요청서          8,9,0
이용제한해지요청서      8,10,0
개발건의                8,11,0
버그제보당첨자          8,12,0

[회원가입]
신규가입                9,1,0
재가입신청              9,2,0

//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''//
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''//
//'''''''''''''''''''''''''''''''''''''''''''''''''''''''''''//

[블루-메인]
메인페이지				13,0,0
이용약관                13,1,0
개인정보취급방침        13,2,0
사이트맵                13,3,0
블루이용동의            13,4,0
블루이용동의해제        13,5,0

[블루-소식]
공지사항                14,1,0
업데이트                14,2,0
이벤트                  14,3,0

[블루-가이드]
게임소개                15,1,0
초보자가이드            15,2,0
플레이가이드            15,3,0

[블루-랭킹]
랭킹                    16,1,0
랭킹가이드              16,2,0

[블루-커뮤니티]
자유게시판              17,1,0
스크린샷                17,2,0

[블루-다운로드]
게임 다운로드           18,1,0

[블루-상점]
상점                    19,1,0
보관함                  19,2,0
프리미엄 쿠폰함         19,3,0
내 캐시                 19,4,0


통합검색                fnGoSearch();
뮤클럽                  fnGoClub();
가맹PC방                fnGoPcBang();

공지사항 상세정보       fnGoNoticeDetail(num);
업데이트 상세정보       fnGoUpdateDetail(num);
이벤트 상세정보         fnGoEventDetail(num);
*/


var CodeDefine = {
    /* 홈 */
    Code_0_0_0: { link: Http.Content + "SiteMain.aspx", name: "홈" },
    Code_0_1_0: { link: Http.Member + "Account/searchAccount.aspx", name: "아이디/비밀번호 찾기", title: "tit_idpwfind.gif" },
    Code_0_2_0: { link: Http.Content + "Information/agreement.aspx", name: "이용약관", title: "tit_agreement.gif" },
    Code_0_3_0: { link: Http.Content + "Information/policy.aspx", name: "개인정보취급방침 및 청소년보호정책", title: "tit_policy.gif" },
    Code_0_4_0: { link: Http.Content + "Information/sitemap.aspx", name: "사이트맵", title: "tit_sitemap.gif" },
    /* 소식 */
    Code_1_0_0: { link: Http.Content + "News/Notice/List.aspx", name: "소식" },
    Code_1_1_0: { link: Http.Content + "News/Notice/List.aspx", name: "공지사항", title: "tit_notice.gif" },
    Code_1_2_0: { link: Http.Content + "News/Update/", name: "업데이트", title: "tit_update.gif" },
    Code_1_3_0: { link: Http.Content + "News/Event/", name: "이벤트", title: "tit_event.gif" },
    Code_1_4_0: { link: Http.Content + "News/Column/List.aspx", name: "칼럼", title: "tit_column.gif" },
    Code_1_5_0: { link: Http.Content + "News/Paperzine/", name: "페이퍼진", title: "tit_paperzine.gif" },
    /* 가이드 */
    Code_2_0_0: { link: Http.Content + "Guide/Encyclopedia/Introduction.aspx", name: "가이드" },
    Code_2_1_0: { link: Http.Content + "Guide/Encyclopedia/Introduction.aspx", name: "게임소개", title: "tit_gameinfo.gif" },
    Code_2_2_0: { link: Http.Content + "Guide/Encyclopedia/InstallAndPlay.aspx", name: "초보자가이드", title: "tit_firstguide.gif" },
    Code_2_3_0: { link: Http.Content + "Guide/Encyclopedia/PlayTip.aspx", name: "게임 시스템", title: "tit_gamesystem.gif" },
    Code_2_4_0: { link: Http.Content + "Guide/Encyclopedia/Quest.aspx", name: "퀘스트", title: "tit_quest.gif" },
    Code_2_5_0: { link: Http.Content + "Guide/ThemeGuide/", name: "테마가이드", title: "tit_themeguide.gif" },
    /* 백과사전 */
    Code_3_0_0: { link: Http.Content + "Guide/Encyclopedia/Character.aspx", name: "백과사전" },
    Code_3_1_0: { link: Http.Content + "Guide/Encyclopedia/Character.aspx", name: "캐릭터", title: "tit_character.gif" },
    Code_3_2_0: { link: Http.Content + "Guide/M_Guide/S_Dictionary/DefaultSkill.asp", name: "스킬", title: "tit_skill.gif" },
    Code_3_3_0: { link: Http.Content + "Guide/M_Guide/S_Dictionary/DefaultUnit.asp", name: "아이템", title: "tit_item.gif" },
    Code_3_4_0: { link: Http.Content + "Guide/Encyclopedia/Map.aspx", name: "지역", title: "tit_area.gif" },
    Code_3_5_0: { link: Http.Content + "Guide/M_Guide/S_Dictionary/MonsterList.asp", name: "몬스터", title: "tit_monster.gif" },
    Code_3_6_0: { link: Http.Content + "Guide/Encyclopedia/NPC.aspx", name: "NPC", title: "tit_npc.gif" },
    /* 뮤티즌광장 */
    Code_4_0_0: { link: Http.Content + "Community/FreeBoard/List.aspx", name: "뮤티즌광장" },
    Code_4_1_0: { link: Http.Content + "Community/FreeBoard/List.aspx", name: "자유게시판", title: "tit_freeboard.gif" },
    Code_4_2_0: { link: Http.Content + "Community/ItemMall/List.aspx", name: "아이템장터", title: "tit_itemshop.gif" },
    Code_4_3_0: { link: Http.Content + "Community/QnABoard/List.aspx", name: "묻고답하기", title: "tit_qna.gif" },
    Code_4_4_0: { link: Http.Content + "Community/Screenshot/List.aspx", name: "스크린샷", title: "tit_screenshot.gif" },
    Code_4_5_0: { link: Http.Content + "Community/Reply/Write.aspx", name: "리플달기", title: "tit_replyapply.gif" },
    Code_4_6_0: { link: Http.Content + "Community/Poll/List.aspx", name: "설문조사", title: "tit_research.gif" },
    /* 자료실 */
    Code_5_0_0: { link: Http.Content + "?p=download", name: "자료실" },
    Code_5_1_0: { link: Http.Content + "?p=download", name: "클라이언트", title: "tit_client.gif" },
    Code_5_2_0: { link: Http.Content + "Download/Graphic/", name: "드라이버", title: "tit_driver.gif" },
    Code_5_3_0: { link: Http.Content + "Download/VOD/", name: "동영상", title: "tit_movie.gif" },
    Code_5_4_0: { link: Http.Content + "Download/WallPaper/", name: "월페이퍼", title: "tit_wallpaper.gif" },
    /* 상점 */
    Code_6_0_0: { link: Http.Payment + "Main/Main_Mu.asp?AccessUrl=/GameShop/MuShopMainFrm.asp", name: "상점" },
    Code_6_1_0: { link: Http.Payment + "Main/Main_Mu.asp?AccessUrl=/GameShop/MuShopMainFrm.asp", name: "상점", title: "tit_store.gif" },
    Code_6_2_0: { link: Http.Payment + "Main/Main_Mu.asp?AccessUrl=/MyPage/Storage/StorageListFrm.asp", name: "보관함", title: "tit_storebox.gif" },
    Code_6_3_0: { link: Http.Payment + "Main/Main_Mu.asp?AccessUrl=/Premium/PremiumCouponListFrm.asp", name: "프리미엄 쿠폰함", title: "tit_premiumcoupon.gif" },
    Code_6_4_0: { link: Http.Payment + "Main/Main_Mu.asp?AccessUrl=/MyPage/CashChargeList/CashChargeListFrm.asp", name: "내 캐시", title: "tit_mycash.gif" },
    /* 내정보 */
    Code_7_0_0: { link: Http.Content + "Mypage/", name: "내정보" },
    Code_7_1_0: { link: Http.Content + "Mypage/", name: "내 정보 메인", title: "tit_mypage.gif" },
    Code_7_2_0: { link: Http.Member + "Account/Modify.aspx", name: "개인정보변경", title: "tit_registaccount.gif" },
    Code_7_3_0: { link: Http.Member + "Account/Introduce.aspx", name: "소개정보변경", title: "tit_registintro.gif" },
    Code_7_4_0: { link: Http.Member + "Account/Password.aspx", name: "비밀번호변경", title: "tit_pwchange.gif" },
    Code_7_5_0: { link: Http.Member + "Account/QnA.aspx", name: "질문답변변경", title: "tit_qnachange.gif" },
    Code_7_6_0: { link: Http.Content + "Mypage/Message/", name: "쪽지함", title: "tit_messagebox.gif" },
    Code_7_7_0: { link: Http.Member + "Sms/Default.aspx", name: "뮤 알리미 관리", title: "tit_smsmanager.gif" },
    Code_7_8_0: { link: Http.Member + "Account/Secession.aspx", name: "개인정보삭제신청", title: "tit_secessionapply.gif" },
    Code_7_9_0: { link: Http.Member + "Account/SelfAuth.aspx", name: "본인인증", title: "tit_selfvalidation.gif" },
    Code_7_10_0: { link: Http.Member + "Account/Nickname.aspx", name: "닉네임 등록", title: "tit_nickname.gif" },

    /* 고객지원 */
    Code_8_0_0: { link: Http.Content + "Support/", name: "고객지원" },
    Code_8_1_0: { link: Http.Content + "Support/", name: "이용안내", title: "tit_useinfo.gif" },
    Code_8_2_0: { link: Http.Content + "Support/FAQ/", name: "FAQ", title: "tit_faq.gif" },
    Code_8_3_0: { link: Http.Content + "Support/UnifyReport/", name: "통합신고", title: "tit_unifyreport.gif" },
    Code_8_4_0: { link: Http.Content + "Support/UnifyInquiry/Write.aspx", name: "통합문의", title: "tit_unifyinquiry.gif" },
    Code_8_5_0: { link: Http.Content + "Support/BugReport/", name: "버그제보", title: "tit_bugreport.gif" },
    Code_8_6_0: { link: Http.Content + "Support/MyAsk/", name: "나의 문의 내역", title: "tit_myask.gif" },
    Code_8_7_0: { link: Http.Content + "Support/OperationPolicy.aspx", name: "운영정책", title: "tit_managementpolicy.gif" },
    Code_8_8_0: { link: Http.Content + "Support/UnifyReport/BadUserList.aspx", name: "불량이용자 명단", title: "tit_baduser.gif" },
    Code_8_9_0: { link: Http.Content + "Support/CancelRequest/ReportWrite.aspx", name: "신고취하 요청서", title: "tit_report.gif" },
    Code_8_10_0: { link: Http.Content + "Support/CancelRequest/BlockWrite.aspx", name: "이용제한 해지 요청서", title: "tit_block.gif" },
    Code_8_11_0: { link: Http.Content + "Support/ProposeDevelop/Write.aspx", name: "개발건의", title: "tit_developapply.gif" },
    Code_8_12_0: { link: Http.Content + "Support/BugReport/BugWinner.asp", name: "버그제보 당첨자", title: "tit_bugwinner.gif" },

    /* 회원가입 */
    Code_9_0_0: { link: Http.Member + "Account/", name: "회원가입" },
    Code_9_1_0: { link: Http.Member + "Account/", name: "회원가입", title: "tit_join.gif" },
    Code_9_2_0: { link: Http.Member + "Account/ReJoinLogin.aspx", name: "재가입", title: "tit_rejoin.gif" },

    /* 랭킹 */
    Code_10_0_0: { link: Http.Content + "Ranking/Gens.aspx", name: "랭킹" },
    Code_10_1_0: { link: Http.Content + "Ranking/Gens.aspx", name: "겐스 랭킹", title: "tit_gensranking_tit_join.gif" },
    Code_10_2_0: { link: Http.Content + "Ranking/HallOfFame.aspx", name: "명예의 전당", title: "tit_honorhall.gif" },

    /* 통합검색 */
    Code_11_0_0: { link: Http.Search + "", name: "통합검색" },
    Code_11_1_0: { link: Http.Search + "", name: "통합검색" },

    /* 클럽 */
    Code_12_0_0: { link: Http.Club + "", name: "클럽" },
    Code_12_1_0: { link: Http.Club + "", name: "클럽" },


    /* 블루 메인 */
    Code_13_0_0: { link: Http.Blue + "SiteMain.aspx", name: "홈" },
    Code_13_1_0: { link: Http.Blue + "Information/agreement.aspx", name: "이용약관", title: "tit_agreement.gif" },
    Code_13_2_0: { link: Http.Blue + "Information/policy.aspx", name: "개인정보취급방침 및 청소년보호정책", title: "tit_policy.gif" },
    Code_13_3_0: { link: Http.Blue + "Information/sitemap.aspx", name: "사이트맵", title: "tit_sitemap.gif" },
    Code_13_4_0: { link: Http.Blue + "Join/Agree.aspx", name: "블루 이용동의", title: "" },
    Code_13_5_0: { link: Http.Blue + "Join/Cancel.aspx", name: "블루 이용동의해제", title: "" },

    /* 블루 소식 */
    Code_14_0_0: { link: Http.Blue + "News/Notice/List.aspx", name: "소식" },
    Code_14_1_0: { link: Http.Blue + "News/Notice/List.aspx", name: "공지사항", title: "tit_notice.gif" },
    Code_14_2_0: { link: Http.Blue + "News/Update/", name: "업데이트", title: "tit_update.gif" },
    Code_14_3_0: { link: Http.Blue + "News/Event/", name: "이벤트", title: "tit_event.gif" },


    /* 블루 가이드 */
    Code_15_0_0: { link: Http.Blue + "Guide/Introduction.aspx", name: "가이드" },
    Code_15_1_0: { link: Http.Blue + "Guide/Introduction.aspx", name: "게임소개", title: "tit_gameinfo.gif" },
    Code_15_2_0: { link: Http.Blue + "Guide/Beginner.aspx", name: "초보자가이드", title: "tit_firstguide.gif" },
    Code_15_3_0: { link: Http.Blue + "Guide/Play.aspx", name: "플레이가이드", title: "tit_gamesystem.gif" },

    /* 블루 랭킹 */
    Code_16_0_0: { link: Http.Blue + "Ranking/Gens.aspx", name: "게임기록실" },
    Code_16_1_0: { link: Http.Blue + "Ranking/Gens.aspx", name: "겐스 랭킹", title: "tit_gensranking.gif" },
    Code_16_2_0: { link: Http.Blue + "Ranking/HallOfFame.aspx", name: "명예의 전당", title: "tit_halloffame.gif" },

    /* 블루 커뮤니티 */
    Code_17_0_0: { link: Http.Blue + "Community/FreeBoard/List.aspx", name: "커뮤니티" },
    Code_17_1_0: { link: Http.Blue + "Community/FreeBoard/List.aspx", name: "자유게시판", title: "tit_freeboard.gif" },
    Code_17_2_0: { link: Http.Blue + "Community/ScreenShot/List.aspx", name: "스크린샷", title: "tit_screenshot.gif" },

    /* 블루 다운로드 */
    Code_18_0_0: { link: Http.Blue + "?p=download", name: "다운로드" },
    Code_18_1_0: { link: Http.Blue + "?p=download", name: "게임 다운로드", title: "tit_client.gif" },

    /* 블루 상점 */
    Code_19_0_0: { link: Http.BlueShop + "Main/Main_Mu.asp?AccessUrl=/GameShop/MuShopMainFrm.asp", name: "상점" },
    Code_19_1_0: { link: Http.BlueShop + "Main/Main_Mu.asp?AccessUrl=/GameShop/MuShopMainFrm.asp", name: "상점", title: "tit_store.gif" },
    Code_19_2_0: { link: Http.BlueShop + "Main/Main_Mu.asp?AccessUrl=/MyPage/Storage/StorageListFrm.asp", name: "보관함", title: "tit_storebox.gif" },
    Code_19_3_0: { link: Http.BlueShop + "Main/Main_Mu.asp?AccessUrl=/Premium/PremiumCouponListFrm.asp", name: "프리미엄 쿠폰함", title: "tit_premiumcoupon.gif" },
    Code_19_4_0: { link: Http.BlueShop + "Main/Main_Mu.asp?AccessUrl=/MyPage/CashChargeList/CashChargeListFrm.asp", name: "내 캐시", title: "tit_mycash.gif" }
}

var Navigation = {
    GoMenu: function (c1, c2, c3) {

        if (c1 == 11 || c1 == 12) {
            alert("해당 메뉴는 사용하실 수 없습니다.");
            return false;
        }
        //if (blnLive) {
            switch (parseInt(c1)) {

                case 10:
                    if (parseInt(c2) == 2) {
                        alert("준비중 입니다.");
                        return;
                    }

                case 16:
                    if (parseInt(c2) == 2) {
                        alert("준비중 입니다.");
                        return;
                    }
                    break;
            }
        //}



        if (!eval("CodeDefine.Code_" + c1 + "_" + c2 + "_" + c3))
            return false;

        var linkEle = eval("CodeDefine.Code_" + c1 + "_" + c2 + "_" + c3 + "['link']");
        if (linkEle.indexOf("window.open") > -1 || linkEle.indexOf("javascript") > -1) {
            eval(linkEle);
        } else {
            location.href = linkEle;
        }
    },
    GoMenuPopup: function (c1, c2, c3) {

        if (c1 == 16 || (c1 == 17 && c2 == 2)) {
            alert("준비중 입니다.     ");
            return false;
        }


        if (!eval("CodeDefine.Code_" + c1 + "_" + c2 + "_" + c3))
            return false;

        var linkEle = eval("CodeDefine.Code_" + c1 + "_" + c2 + "_" + c3 + "['link']");
        if (linkEle.indexOf("window.open") > -1 || linkEle.indexOf("javascript") > -1) {
            eval(linkEle);
        } else {
            window.open(linkEle, "blue");
        }
    },
    LocationBar: function (c1, c2, c3) {
        Navigation.SubVisual(c1); //visual

        if (!eval("CodeDefine.Code_" + c1 + "_" + c2 + "_" + c3))
            return false;

        this.ArrayCode = [];
        if (c1 < 13) {
            this.ArrayCode.push("0_0_0");
            if (c1 != 0) this.ArrayCode.push(c1 + "_0_0");
        } else { //BLUE 사이트
            this.ArrayCode.push("13_0_0");
            if (c1 != 13) this.ArrayCode.push(c1 + "_0_0");
        }

        if (c2 != 0) this.ArrayCode.push(c1 + "_" + c2 + "_0");
        if (c3 != 0) this.ArrayCode.push(c1 + "_" + c2 + "_" + c3);

        Navigation.LocationTitle(this.ArrayCode[this.ArrayCode.length - 1]); //title
        var objEle = document.getElementById("txtlocation");
        if (!objEle) return false;
        var strEle = "";

        for (var i = 0; i < this.ArrayCode.length; i++) {
            if (i != 0) { strEle = strEle + " &gt; "; }
            strEle = strEle + "<a href=\"#\" onclick=\"Navigation.GoMenu(" + this.ArrayCode[i].replace(/_/g, ",") + "); return false;\">"
            if (i == this.ArrayCode.length - 1) { strEle = strEle + "<strong>"; }
            strEle = strEle + eval("CodeDefine.Code_" + this.ArrayCode[i] + "['name']")
            if (i == this.ArrayCode.length - 1) { strEle = strEle + "</strong>"; }
            strEle = strEle + "</a>";
        }
        objEle.innerHTML = strEle;
    },
    LocationTitle: function (arrEle) {
        var objEle = document.getElementById("titlocation");
        if (!objEle) return false;
        objEle.src = objEle.src + eval("CodeDefine.Code_" + arrEle + "['title']");
        objEle.alt = eval("CodeDefine.Code_" + arrEle + "['name']");
    },
    SubVisual: function (c1) {
        var strClassEle;
        var objEle = document.getElementById("container");
        if (!objEle) return false;
        switch (c1) {
            case 1: strClassEle = "sub_news"; break; //소식
            case 2: strClassEle = "sub_guide"; break; //가이드
            case 3: strClassEle = "sub_encyclopedia"; break; //백과사전
            case 4: strClassEle = "sub_mutizen"; break; //뮤티즌광장
            case 5: strClassEle = "sub_pds"; break; //자료실
            case 6: strClassEle = "sub_store"; break; //상점
            case 7: strClassEle = "sub_mypage"; break; //내정보
            case 8: strClassEle = "sub_support"; break; //고객지원
            default:
                strClassEle = "sub_pds";
                break;
        }
        objEle.className = strClassEle;
    }
}




function fnGoMenu(intSelNum, intSNum) {

    if (intSelNum == 12 && intSNum == 1) { // 클럽
        fnGoClub();
        return false;
    }
    else {
        Navigation.GoMenu(intSelNum, intSNum, 0);
    }

}




function fnServerInfo(si) {
    switch (si) {
        case "Blue":
            strServer = Http.Blue
            break;
        default:
            strServer = Http.Content
            break;
    }
    return strServer;
}
// 메인
function fnGoMain(si) {
    strLink = fnServerInfo(si) + "SiteMain.aspx";
    document.location.href = strLink;
}

// 클라이언트 다운로드
function fnGoClientDownload(si) {
    strLink = fnServerInfo(si) + "?p=download";
    document.location.href = strLink;
}

// 공지사항 상세정보
function fnGoNoticeDetail(intNum, si) {
    strLink = fnServerInfo(si) + "News/Notice/Content.aspx?Seq=" + intNum;
    document.location.href = strLink;
}

// 업데이트 상세정보
function fnGoUpdateDetail(intNum, si) {
    strLink = fnServerInfo(si) + "News/Update/Content.aspx?Seq=" + intNum;
    document.location.href = strLink;
}

// 이벤트 상세정보
function fnGoEventDetail(intNum, si) {
    strLink = fnServerInfo(si) + "News/Event/Content.aspx?Seq=" + intNum;
    document.location.href = strLink;
}

// 통합검색
function fnGoSearch() {
    alert("해당 메뉴는 사용하실 수 없습니다.");
    return false;
    strLink = Http.Search;
    document.location.href = strLink;
}

// 클럽
function fnGoClub() {
    alert("해당 메뉴는 사용하실 수 없습니다.");
    return false;
    window.open(Http.Club, "club");
}

// PC방

function fnGoPcBang(si) {
    //strLink = "http://pcbang.webzen.com/Common/PCSearch/popfindMuPCBang.asp";
    strLink = "http://pcbang.webzen.co.kr/Common/PCSearch/findPCBang.asp";
    window.open(strLink, "pcbang", "scrollbars=yes, resizable=no,width=100,height=100, top=100, left=100");
}

// 로그인

function fnGoLogin(si) {
    if (si == undefined)
        si = "Content";
    document.location.href = Http.Member + "Login/LoginFrm.aspx?si=" + si;
}

// 로그아웃
function fnGoLogout(si) {
    if (si == undefined)
        si = "Content";

    document.location.href = Http.Member + "Login/Logout.aspx?si=" + si;
}

