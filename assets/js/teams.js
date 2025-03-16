let teamName=document.body.className,url="https://corsproxy.io/?url=https://api-web.nhle.com/v1/club-stats/"+teamName+"/20242025/2";async function fetchSkaterStats(){try{var t=await fetch(url);if(!t.ok)throw new Error("HTTP error! status: "+t.status);var e=await t.json();populateSkaters(e),populateGoalies(e)}catch(t){console.error("Error fetching data:",t)}}function populateSkaters(t){let s=document.querySelector("#skater-stats-table tbody");s.innerHTML="",t.skaters.forEach(t=>{var e=2*t.goals+t.assists,a=document.createElement("tr");a.innerHTML=`
          <td>${t.firstName.default} ${t.lastName.default}</td>
          <td class="headshot"><img src="${t.headshot}" alt="" /></td>
          <td>${t.goals}</td>
          <td>${t.assists}</td>
          <td>${t.points}</td>
          <td>${e}</td>
        `,s.appendChild(a)})}function populateGoalies(t){let s=document.querySelector("#goalie-stats-table tbody");s.innerHTML="",t.goalies.forEach(t=>{var e=document.createElement("tr"),a=2*t.wins+3*t.shutouts;e.innerHTML=`
          <td>${t.firstName.default} ${t.lastName.default}</td>
          <td class="headshot"><img src="${t.headshot}" alt="" /></td>
          <td>${t.wins}</td>
          <td>${t.shutouts}</td>
          <td>${a}</td>
        `,s.appendChild(e)})}fetchSkaterStats();