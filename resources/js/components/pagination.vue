<template>
<ul :class="paginationClasses.ul" v-if="pageCount">
    <li v-if="paginationLabels.first"
        :class="`${paginationClasses.li} ${hasFirst ? paginationClasses.liDisable : ''}`">
        <a @click="first"
                :disabled="hasFirst"
                :class="`${paginationClasses.link} ${hasFirst ? paginationClasses.linkDisable : ''}`"
                v-html="paginationLabels.first"></a>
    </li>
    <li v-if="paginationLabels.prev"
        :class="`${paginationClasses.li} ${hasFirst ? paginationClasses.liDisable : ''}`">
        <a @click="prev"
                :disabled="hasFirst"
                :class="`${paginationClasses.link} ${hasFirst ? paginationClasses.linkDisable : ''}`"
                v-html="paginationLabels.prev"></a>
    </li>
    <li v-show="rangeFirstPage !== 1"
        :class="paginationClasses.li">
        <a @click="goto(1)"
                :class="paginationClasses.link">1</a>
    </li>
    <li v-show="rangeFirstPage === 3"
        :class="paginationClasses.li">
        <a @click="goto(2)"
                :class="paginationClasses.link">2</a>
    </li>
    <li v-show="rangeFirstPage !== 1 && rangeFirstPage !== 2 && rangeFirstPage !== 3"
        :class="`${paginationClasses.li} ${paginationClasses.liDisable}`">
        <span :class="`${paginationClasses.link} ${paginationClasses.linkDisable}`">...</span>
    </li>
    <!-- range start -->
    <li v-for="page in range"
        :key="page"
        :class="`${paginationClasses.li} ${hasActive(page) ? paginationClasses.liActive : ''}`">
        <a @click="goto(page)"
                :class="`${paginationClasses.link} ${hasActive(page) ? paginationClasses.linkActive : ''}`">{{ page }}</a>
    </li>
    <!-- range end -->
    <li v-show="rangeLastPage !== pageCount && rangeLastPage !== (pageCount - 1) && rangeLastPage !== (pageCount - 2)"
        :class="`${paginationClasses.li} ${paginationClasses.liDisable }`">
        <span :class="`${paginationClasses.link} ${paginationClasses.linkDisable }`">...</span>
    </li>
    <li v-show="rangeLastPage === (pageCount - 2)"
        :class="paginationClasses.li">
        <a @click="goto(pageCount - 1)"
                :class="paginationClasses.link">{{ (pageCount - 1) }}</a>
    </li>
    <li v-if="rangeLastPage !== pageCount"
        :class="paginationClasses.li">
        <a @click="goto(pageCount)"
                :class="paginationClasses.link">{{ pageCount }}</a>
    </li>
    <li v-if="paginationLabels.next"
        :class="`${paginationClasses.li} ${hasLast ? paginationClasses.liDisable : ''}`">
        <a @click="next"
                :disabled="hasLast"
                :class="`${paginationClasses.link} ${hasLast ? paginationClasses.linkDisable : ''}`"
                v-html="paginationLabels.next"></a>
    </li>
    <li v-if="paginationLabels.last"
        :class="`${paginationClasses.li} ${hasLast ? paginationClasses.liDisable : ''}`">
        <a @click="last"
                :disabled="hasLast"
                :class="`${paginationClasses.link} ${hasLast ? paginationClasses.linkDisable : ''}`"
                v-html="paginationLabels.last"></a>
    </li>
</ul>
</template>

<script>
  const rangeMax = 3;
  const defaultClasses = {
    ul: 'pagination',
    li: 'paginate_button page-item',
    liActive: 'active',
    liDisable: 'disabled',
    link: 'page-link',
    linkActive: 'page-link',
    linkDisable: 'page-link'
  };
  const defaultLabels = {
    first: '&laquo;',
    prev: '&lsaquo;',
    next: '&rsaquo;',
    last: '&raquo;'
  };

  export default {
    props: {
      value: {  // current page
        type: Number,
        required: true
      },
      pageCount: { // page numbers
        type: Number,
        required: true
      },
      classes: {
        type: Object,
        required: false,
        default: () => ({})
      },
      labels: {
        type: Object,
        required: false,
        default: () => ({})
      },
      callBack: {
        type: Function,
        required: false,
        default: null
      },
      callBackData: {
        type: Object,
        required: false,
        default: null
      }
    },

    data() {
      return {
        paginationClasses: {
          ...defaultClasses,
          ...this.classes
        },
        paginationLabels: {
          ...defaultLabels,
          ...this.labels
        }
      }
    },

    mounted() {
      if (this.value > this.pageCount) {
        this.$emit('input', this.pageCount);
      }
    },

    computed: {
      rangeFirstPage() {
        if (this.value === 1) {
          return 1;
        }

        if (this.value === this.pageCount) {
          if ((this.pageCount - rangeMax) < 0) {
            return 1;
          }
          else {
            return this.pageCount - rangeMax + 1;
          }
        }

        return (this.value - 1);
      },

      rangeLastPage() {
        return Math.min(this.rangeFirstPage + rangeMax - 1, this.pageCount);
      },

      range() {
        let rangeList = [];
        for (let page = this.rangeFirstPage; page <= this.rangeLastPage; page+= 1) {
            rangeList.push(page);
        }
        return rangeList;
      },

      hasFirst() {
        return (this.value === 1);
      },

      hasLast() {
        return (this.value === this.pageCount);
      },
    },

    watch: {
      value: function () {
        this.$emit('change');
        if(this.callBack && this.callBackData && this.callBackData.module && this.value) {
            var params = (typeof this.callBackData.params == 'object') ? this.callBackData.params : {};
            params['page'] = this.value;
            params = Object.keys(params).map(key => key + '=' + params[key]).join('&');

            this.callBack(this.callBackData.module + '?' + params);
        }
      }
    },

    methods: {
      first() {
        if (!this.hasFirst) {

          this.$emit('input', 1);
        }
      },

      prev() {
        if (!this.hasFirst) {
          this.$emit('input', (this.value - 1));
        }
      },

      goto(page) {
        this.$emit('input', page);
      },

      next() {
        if (!this.hasLast) {
          this.$emit('input', (this.value + 1));
        }
      },

      last() {
        if (!this.hasLast) {
          this.$emit('input', this.pageCount);
        }
      },

      hasActive(page) {
        return (page === this.value);
      },
    }
  }
</script>
