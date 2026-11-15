with open(r'c:\laragon\www\sistema-dg-local\resources\views\productos\index.blade.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

idx_delete_start = None
idx_delete_end = None
for i, line in enumerate(lines):
    if 'id="modalEliminarProducto"' in line:
        for j in range(i, len(lines)):
            if lines[j].strip() == '</div>' and lines[j-1].strip() == '</div>' and lines[j-2].strip() == '</div>':
                idx_delete_start = j + 1
                break
        break

for i in range(idx_delete_start, len(lines)):
    if '@endauth' in lines[i]:
        idx_delete_end = i
        break

print(f'Deleting from line {idx_delete_start+1} to {idx_delete_end}')
print('First line to delete:', repr(lines[idx_delete_start]))
print('Last line to delete:', repr(lines[idx_delete_end-1]))

del lines[idx_delete_start:idx_delete_end]

with open(r'c:\laragon\www\sistema-dg-local\resources\views\productos\index.blade.php', 'w', encoding='utf-8') as f:
    f.writelines(lines)
print('Done!')
