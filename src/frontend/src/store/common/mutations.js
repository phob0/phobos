export function removeItem(state, payload) {
  const ix = state.items.findIndex(item => item.id === payload.id)
  if (ix >= 0) {
    state.items.splice(ix, 1)
  }
}
