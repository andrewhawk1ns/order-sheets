import { shallowMount, createLocalVue } from '@vue/test-utils';
import Home from '../../views/Home.vue';

describe('Home', () => {
  it('renders the Home page without error', () => {
    const localVue = createLocalVue();
    const wrapper = shallowMount(Home, {localVue});
    const div = wrapper.find('div')
    expect(div.exists()).toBe(true)
  })
});
