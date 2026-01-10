import { defineStore } from 'pinia';
import _ from 'lodash';
import { date } from 'quasar';
import TagAPIv1 from '../modules/api/v1/tag';
import { v4 as uuid } from 'uuid';
import { getChangedPositions } from '../modules/helpers';
import { METRICS, trackEvent } from '../modules/metrics';
import { useUsersStore } from './users';
import { useTransactionsStore } from './transactions';
import { useBudgetsStore } from './budgets';
import { useLocalStorage } from '@vueuse/core';
import { StorageKeys } from '../modules/storage';
import { RemovableRef } from '@vueuse/shared';
import { CreateTagDto, TagDto, TagItemDto, TagListDto, UpdateTagDto } from '../modules/api/v1/dto/tag.dto';
import { BooleanType, DateString, Id } from '@shared/types';
import { computed } from 'vue';
import { DATE_TIME_FORMAT } from '../modules/constants';

interface ChangeTagSortModeForm {
  type: string,
  direction: 'asc' | 'desc'
}

export const useTagsStore = defineStore('tags', () => {
  const tags = useLocalStorage(StorageKeys.TAGS, []) as RemovableRef<TagDto[]>;
  const tagsLoadedAt = useLocalStorage(StorageKeys.TAGS_LOADED_AT, null) as RemovableRef<DateString | null>;

  const isTagsLoaded = computed(() => !!tagsLoadedAt.value);
  const ownTags = computed(() => {
    const userStore = useUsersStore();
    return _.orderBy(_.filter(_.cloneDeep(tags.value), { ownerUserId: userStore.userId }), 'position', 'asc') as TagDto[];
  });
  const tagsOrdered = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(tags.value), { isArchived: BooleanType.FALSE }), 'position', 'asc') as TagDto[];
  });
  const tagsArchived = computed(() => {
    return _.orderBy(_.filter(_.cloneDeep(tags.value), { isArchived: BooleanType.TRUE }), 'updatedAt', 'desc') as TagDto[];
  });

  function fetchTags() {
    return TagAPIv1.getList((response: TagListDto) => {
      TAGS_INIT(response.items);
    }, (error: any) => {
      return error;
    });
  }

  function createTag(params: CreateTagDto) {
    trackEvent(METRICS.TAG_CREATE);
    params.id = uuid();
    const userStore = useUsersStore();
    const foundTag = _.findLast(_.filter(tags.value, { ownerUserId: userStore.userId }), (item: any) => {
      return item.name.toLowerCase() === params.name.toLowerCase();
    });
    if (foundTag) {
      return new Promise((resolve, reject) => {
        if (!params.name) {
          reject('Tag is empty');
          return;
        }

        if (foundTag) {
          resolve(foundTag);
        }
      });
    }

    const budgetStore = useBudgetsStore();
    return TagAPIv1.create(params, (response: TagItemDto) => {
      TAG_CREATE(response.item);
      budgetStore.resetCachedBudget();
      return response.item;
    }, (error: any) => {
      return error;
    });
  }

  function updateTag(params: UpdateTagDto) {
    trackEvent(METRICS.TAG_UPDATE);
    const foundTag = _.findLast(_.filter(tags.value, { ownerUserId: useUsersStore().userId }), (item: TagDto) => {
      return item.name.toLowerCase() === params.name.toLowerCase() && item.id !== params.id;
    });
    if (foundTag) {
      return new Promise((resolve, reject) => {
        reject('Tag name exists');
      });
    }

    return TagAPIv1.update(params, (response: any) => {
      TAG_UPDATE(params);
      return !!response.data;
    }, (error: any) => {
      return error;
    });
  }

  function deleteTag(tagId: Id) {
    trackEvent(METRICS.TAG_DELETE);
    const transactionsStore = useTransactionsStore();
    const budgetStore = useBudgetsStore();
    return TagAPIv1.delete(tagId, (response: any) => {
      if (!!response.data) {
        TAG_DELETE(tagId);
        transactionsStore.TRANSACTIONS_TAG_DELETE(tagId);
        budgetStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      return error;
    });
  }

  function archiveTag(tagId: Id) {
    trackEvent(METRICS.TAG_ARCHIVE);
    const budgetStore = useBudgetsStore();
    return TagAPIv1.archive(tagId, (response: any) => {
      if (!!response.data) {
        TAG_UPDATE_ARCHIVE({ id: tagId, isArchived: BooleanType.TRUE });
        budgetStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      return error;
    });
  }

  function unarchiveTag(tagId: Id) {
    trackEvent(METRICS.TAG_UNARCHIVE);
    const budgetStore = useBudgetsStore();
    return TagAPIv1.unarchive(tagId, (response: any) => {
      if (!!response.data) {
        TAG_UPDATE_ARCHIVE({ id: tagId, isArchived: BooleanType.FALSE });
        budgetStore.resetCachedBudget();
        return true;
      }
      return false;
    }, (error: any) => {
      return error;
    });
  }

  function changeTagsSortMode(options: ChangeTagSortModeForm) {
    trackEvent(METRICS.TAG_CHANGE_ORDER, options);
    let orderedTags = _.cloneDeep(ownTags.value);
    const usageCounts: { [key: string]: number } = {};
    if (options.type === 'name') {
      orderedTags = _.orderBy(orderedTags, ['name'], [options.direction]);
    } else if (options.type === 'count') {
      const transactionsStore = useTransactionsStore();
      orderedTags.forEach((item) => {
        usageCounts[item.id] = _.filter(transactionsStore.transactions, { tagId: item.id }).length;
      });
      orderedTags = _.orderBy(orderedTags, item => usageCounts[item.id], [options.direction]);
    }
    const tagIds: Id[] = [];
    orderedTags.forEach((item: TagDto) => {
      tagIds.push(item.id);
    });
    return orderTagList(tagIds);
  }

  function orderTagList(tagIds: string[]) {
    trackEvent(METRICS.TAG_ORDER_LIST);
    const changes = getChangedPositions(tags.value, tagIds);
    if (!changes.length) {
      return new Promise((resolve, reject) => {
        reject('No changes');
      });
    }
    return TagAPIv1.orderList(changes, (response: TagListDto) => {
      TAGS_INIT(response.items);
      return response.items;
    }, (error: any) => {
      return error;
    });
  }

  function TAGS_INIT(items: TagDto[]) {
    tags.value = items;
    tagsLoadedAt.value = date.formatDate(new Date(), DATE_TIME_FORMAT);
  }

  function TAG_CREATE(options: any) {
    options.isArchived = BooleanType.FALSE;
    tags.value.push(options);
  }

  function TAG_UPDATE(options: any) {
    const copyTags = _.cloneDeep(tags.value);
    const tag = _.cloneDeep(_.find(copyTags, { id: options.id }));
    _.remove(copyTags, (item) => {
      return item.id === options.id;
    });
    copyTags.push(_.extend(tag, options));
    tags.value = copyTags;
  }

  function TAG_DELETE(id: Id) {
    const copyTags = _.cloneDeep(tags.value);
    _.remove(copyTags, (item) => {
      return item.id === id;
    });
    tags.value = copyTags;
  }

  function TAG_UPDATE_ARCHIVE(options: { id: Id, isArchived: BooleanType }) {
    const tag: any = _.cloneDeep(_.find(tags.value, { id: options.id }));
    tag.isArchived = options.isArchived;
    const copyTags = _.cloneDeep(tags.value);
    _.remove(copyTags, (item) => {
      return item.id === options.id;
    });
    copyTags.push(tag);
    tags.value = copyTags;
  }

  return {
    tags,
    tagsLoadedAt,
    isTagsLoaded,
    ownTags,
    tagsOrdered,
    tagsArchived,
    fetchTags,
    createTag,
    updateTag,
    deleteTag,
    archiveTag,
    unarchiveTag,
    changeTagsSortMode,
    orderTagList
  };
});
