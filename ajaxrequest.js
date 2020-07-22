import $ from 'jquery';

class Search {
  // 1. describe and create/initiate our object
  constructor() {
    this.addSearchHTML();
    this.resultsDiv = $("#search-overlay__results");
    this.openButton = $(".js-search-trigger");
    this.closeButton = $(".search-overlay__close");
    this.searchOverlay = $(".search-overlay");
    this.searchField = $("#search-term");
    this.events();
    this.isOverlayOpen = false;
    this.isSpinnerVisible = false;
    this.previousValue;
    this.typingTimer;
  }

  getResults() {
    $.getJSON(universityData.root_url + '/wp-json/mainurl/v1/search?term=' + this.searchField.val(),(results) => {
        this.resultsDiv.html(`
         <div class="row">
            <div class="one-third">
              <h2 class="search-overlay__section-title">General Information</h2>
              ${results.generalInfo.length ? '<ul class="link-list min-list">' : '<p>No general information matches that search.</p>'}
                ${results.generalInfo.map(item => `<li><a href="${item.permaLink}">${item.title}</a> ${item.postType == 'post' ? `by ${item.authorName}` : ''}</li>`).join('')}
              ${results.generalInfo.length ? '</ul>' : ''}
            </div>
            <div class="one-third">
              <h2 class="search-overlay__section-title">Programes</h2>
               ${results.programs.length ? '<ul class="link-list min-list">' : `<p>No Programes matches that search.View <a href="${universityData.root_url}/programs">all Programs</a></p>`}
                ${results.programs.map(item => `<li><a href="${item.permaLink}">${item.title}</a></li>`).join('')}
              ${results.programs.length ? '</ul>' : ''}
              <h2 class="search-overlay__section-title">Professors</h2>
               ${results.professors.length ? '<ul class="professor-cards">' : '<p>No professors matches that search.</p>'}
                ${results.professors.map(item => `
                  <li class="professor-card__list-item">
                    <a class="professor-card" href="${item.permaLink}">
                      <img class="professor-card__image" src="${item.image}">
                      <span class="professor-card__name">${item.title}</span>
                    </a>
                  </li>
                  `).join('')}
              ${results.professors.length ? '</ul>' : ''}
            </div>
            <div class="one-third">
              <h2 class="search-overlay__section-title">Campuses</h2>
              ${results.campuses.length ? '<ul class="link-list min-list">' : `<p>No Campuses matches that search.View <a href="${universityData.root_url}/campuses">all Campuses</a></p>`}
                ${results.campuses.map(item => `<li><a href="${item.permaLink}">${item.title}</a></li>`).join('')}
              ${results.campuses.length ? '</ul>' : ''}
              <h2 class="search-overlay__section-title">Events</h2>
                ${results.events.length ? '' : `<p>No Events matches that search.View <a href="${universityData.root_url}/events">all Events</a></p>`}
                ${results.events.map(item => `
                    <div class="event-summary">
                      <a class="event-summary__date t-center" href="${item.permaLink}">
                        <span class="event-summary__month">${item.month}</span>
                        <span class="event-summary__day">${item.day}</span>
                      </a>
                      <div class="event-summary__content">
                        <h5 class="event-summary__title headline headline--tiny"><a href="${item.permaLink}">${item.title}</a></h5>
                        <p>${item.description}<a href="${item.permaLink}" class="nu gray">Learn more</a></p>
                      </div>
                  </div>
                `).join('')}
              
            </div>
         </div>
      `);
        this.isSpinnerVisible = false;
    });
  }
}
export default Search;
