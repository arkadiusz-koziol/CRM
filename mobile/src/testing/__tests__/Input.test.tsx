import React from 'react';
import { render, fireEvent } from '@/testing/utils/testUtils';
import { Input } from '@/shared/ui/Input';

describe('Input', () => {
  it('renders correctly', () => {
    const { getByPlaceholderText } = render(
      <Input placeholder="Enter text" />,
    );
    expect(getByPlaceholderText('Enter text')).toBeTruthy();
  });

  it('renders with label', () => {
    const { getByText } = render(
      <Input label="Test Label" placeholder="Enter text" />,
    );
    expect(getByText('Test Label')).toBeTruthy();
  });

  it('shows error message', () => {
    const { getByText } = render(
      <Input error="This is an error" placeholder="Enter text" />,
    );
    expect(getByText('This is an error')).toBeTruthy();
  });

  it('shows helper text', () => {
    const { getByText } = render(
      <Input helperText="This is helper text" placeholder="Enter text" />,
    );
    expect(getByText('This is helper text')).toBeTruthy();
  });

  it('calls onChangeText when text changes', () => {
    const onChangeText = jest.fn();
    const { getByPlaceholderText } = render(
      <Input placeholder="Enter text" onChangeText={onChangeText} />,
    );
    
    const input = getByPlaceholderText('Enter text');
    fireEvent.changeText(input, 'test text');
    expect(onChangeText).toHaveBeenCalledWith('test text');
  });

  it('shows required indicator', () => {
    const { getByText } = render(
      <Input label="Test Label" required placeholder="Enter text" />,
    );
    expect(getByText('*')).toBeTruthy();
  });
});
